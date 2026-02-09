<?php

namespace DrSAS\BladeComponents\Livewire;

use DrSAS\BladeComponents\Support\Str;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use ReflectionMethod;

class SearchableInput extends Component
{
    // Instance du modèle Eloquent utilisée pour la recherche.
    public $modelInstance;

    // Le texte entré par l'utilisateur pour la recherche.
    public string $userInput = '';

    // Nombre de caractères à saisir avant de commencer à suggérer des choses
    public int|false $minCharacters = 3;

    // Méthode personnalisée à appeler pour la recherche.
    public ?string $customSearchMethod = null;

    // Les résultats de recherche à suggérer à l'utilisateur.
    public array|Collection $searchSuggestions = [];

    // Les colonnes de la base de données à consulter pour la recherche.
    public null|string|array $searchInColumns = ['id'];

    // Le(s) champ(s) à afficher dans les suggestions.
    public string|array $displayFieldsInSuggestions = 'id';

    // Le champ à retourner comme valeur de l'input après sélection.
    public string $valueFieldFromSelection = 'id';

    // Le champ à retourner comme valeur de l'input HIDDEN après sélection.
    public string $finalValueFromSelection = 'id';

    // Les champs à inclure dans les résultats de recherche (similaire à SELECT en SQL).
    public array $fieldsToSelect = [];

    // Texte du label pour l'input.
    public string $label = '';

    // Attribut 'name' pour l'élément input HTML.
    public string $name = '';

    // Attribut 'value' pour l'input hidden qui est sensé être celui utilisé quand on submit un formulaire.
    #[Modelable] // <--- Pour permettre à un parent de prendre cette information
    public string $value = '';

    // Si l'input est requis (attribut 'required').
    public bool $required = false;

    // Placeholder pour l'input.
    public string $placeholder = '';

    // Classe CSS pour l'icône dans l'input.
    public string $iconClass = 'fa-magnifying-glass';

    // Classe CSS pour l'input principal
    public string $class = '';

    // Nombre maximum de résultats à afficher dans les suggestions.
    public int $maxSuggestions = 10;

    // Si renseigné, va générer un lien sur les suggestions dont le href sera basé sur le nom de la méthode du modèle.
    public ?string $linkTo = null;

    // Le texte à afficher en dessus de l'input si on en a besoin.
    public string|false $description = false;

    public function mount(
        string $model,
        $method = null
    ) {

        if (
            !class_exists(class: $model)
            || !is_subclass_of($model, Model::class)
        ) {
            throw new \Exception("{$model} n'est pas un modèle valide.");
        }

        $this->modelInstance = new $model;

        if ($method !== null) {
            if (!method_exists($this->modelInstance, $method)) {
                throw new \Exception("{$method} n'est pas une méthode existante dans " . $model::class);
            }

            $reflection = new ReflectionMethod($this->modelInstance, $method);
            if (!$reflection->isPublic()) {
                throw new \Exception("La méthode " . $model::class . "::{$method}() ne peut pas être exécutée. Est-ce qu'elle est bien publique ?");

            }
            $this->customSearchMethod = $method;
        }

        if (!is_array($this->searchInColumns)) {
            $this->searchInColumns = [$this->searchInColumns];
        }

        if (!is_array($this->displayFieldsInSuggestions)) {
            $this->displayFieldsInSuggestions = [$this->displayFieldsInSuggestions];
        }

        // Si on a pas de fields de renseigné, alors on retourne par défaut la PK
        // ainsi que les champs passés en dépendance dans l'appel du component.
        if (count(value: $this->fieldsToSelect) === 0) {
            $this->fieldsToSelect = array_unique(
                array_merge(
                    [
                        ($this->modelInstance)->getKeyName(),
                        $this->valueFieldFromSelection,
                    ],
                    $this->getDisplayFieldsInSuggestionsValues()
                )
            );
        }

        // Si l'input se termine par [] alors on doit inclure un identifiant unique
        // car ce component va retourner lui-même un array : [input, value]
        if (str_ends_with($this->name, '[]')) {
            $uuid = uniqid();
            $this->name = str_replace('[]', "[{$uuid}]", $this->name);
        }

        // Si je détecte qu'une value est passée au montage, alors je vais remplir le champ avec la valeur « humaine »
        if (!empty($this->value)) {
            $item = $this->modelInstance::where($this->finalValueFromSelection, '=', $this->value);
            if ($item->exists()) {
                $item = $item
                        ->get($this->fieldsToSelect)
                        ->first();

                $this->selectSuggestion(
                    $item->{$this->valueFieldFromSelection},
                    $item->{$this->finalValueFromSelection}
                );
            } else {
                // Si je ne trouve pas ce que je veux, c'est que c'est du custom.
                // TODO : Faire en sorte que si on ne veut pas de ce comportement, ça tire une exception à la place
                $this->selectSuggestion(
                    $this->value,
                    ''
                );
            }
        }
    }

    public function updatedUserInput($value)
    {
        if (
            empty($value)
            || (
                $this->minCharacters !== false
                && strlen($value) < $this->minCharacters
            )
        ) {
            $this->clearSuggestions();
            $this->value = ''; // On vide également le champ hidden sinon l'information persistera en cas de submit
            return;
        }

        if ($this->customSearchMethod === null) {
            $builder = $this->modelInstance::where(
                function ($query) use ($value) {
                    foreach ($this->searchInColumns as $column) {
                        if (str_starts_with($column, '!')) {
                            // On force la vérification exacte
                            $column = substr($column, 1);
                            $query->orWhere($column, '=', $value);
                        } else {
                            $query->orWhere($column, 'LIKE', '%' . Str::escapeLike($value) . '%');
                        }
                    }
                }
            );

            $this->optimizeOrderBy(
                $builder,

                // On considère ici que la valeur à exploiter in-fine
                // est celle qui nous importe pour optimiser l'ordonnance.
                $this->valueFieldFromSelection,

                Str::escapeLike($value)
            );
        } else {
            $builder = $this->modelInstance->{$this->customSearchMethod}(
                $this,
                $value
            );
        }

        $suggestions = $builder->take($this->maxSuggestions);

        // TODO : Amélioration possible ici --> On déclenche ce scénario AUSSI si on ne trouve aucune taxonomie qui
        // prend la même valeur que $this->value (sinon on entrera pas dans cette condition si y'a des suggestions qui
        // s'y rapprochent).
        if (!$suggestions->exists()) {
            $this->clearSuggestions();
            $this->value = '';
            return;
        }

        $this->searchSuggestions = $suggestions->get($this->fieldsToSelect);
    }

    public function selectSuggestion($suggestionValue, $finalValue)
    {
        if ($this->linkTo !== null) {
            if (method_exists($this->modelInstance, $this->linkTo)) {
                $link = $this->modelInstance->findOrFail($finalValue)->{$this->linkTo}();
            } else {
                // Fallback : juste un string
                $link = $this->linkTo;
            }

            return redirect($link);
        }

        $this->userInput = $suggestionValue;
        $this->value = $finalValue;
        $this->clearSuggestions(); // On efface la liste car la sélection est faite.
    }

    public function render()
    {
        return view('blade-components::livewire.searchable-input');
    }

    /**
     * Optimizes the order of query results based on how closely they match a specified value.
     *
     * This method adjusts the SQL query to sort results based on the specified column. It prioritizes:
     * - Exact matches to the specified value.
     * - Entries where the column value starts with the specified value.
     * - Entries where the column value contains the specified value anywhere.
     * - All other entries.
     *
     * @param Builder $query The query builder instance, passed by reference.
     * @param string $column The name of the column to apply the sorting logic.
     * @param string $value The value to match against the column.
     *
     * @return Builder Returns the modified query builder instance for chaining.
     */
    private function optimizeOrderBy(
        Builder &$query,
        string $column,
        string $value
    ): Builder {
        if (!in_array($column, $this->searchInColumns)) {
            throw new InvalidArgumentException("Invalid column name : " . $column);
        }

        // On tente de réduire les chances d'injections ici.
        $column = preg_replace('/[^a-zA-Z0-9_]+/', '', $column);

        return $query->orderByRaw(<<<SQL
                    CASE
                        WHEN `$column` = ? THEN 1
                        WHEN `$column` LIKE ? THEN 2
                        WHEN `$column` LIKE ? THEN 3
                        ELSE 4
                    END
                SQL,
                [
                    $value,
                    $value . '%',
                    '%' . $value . '%'
                ]);
    }

    private function clearSuggestions()
    {
        $this->searchSuggestions = [];
    }

    private function getDisplayFieldsInSuggestionsValues()
    {
        return array_map(
            static function ($field) {
                return $field['value'];
            },
            array_filter(
                // Ce filtre s'assure de donner que des clés `value` sinon on aurait une erreur PHP si elle est absente.
                $this->displayFieldsInSuggestions,
                fn ($field) => isset($field['value'])
            ),
        );
    }
}