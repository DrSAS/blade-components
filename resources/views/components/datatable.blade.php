@props([
    'pagination' => true,
    'searchable' => true,
    'data' => [],
])

<div {{ $attributes->merge(['class' => '']) }}>
    <table id="datatable-{{ $uid }}"></table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let uid = "datatable-{{ $uid }}"
        if (document.getElementById(uid) && typeof DataTable !== 'undefined') {
            const data = {!! json_encode($data->getVisibleValues()) !!}.map(row => [...row, ''])
            const itemIds = {!! json_encode($data->getIds()) !!}
            const dataTable = new DataTable(`#${uid}`, {
                paging: {{ json_encode($pagination) }},
                searchable: {{ json_encode($searchable) }},
                data: {
                    "headings": {!! 
                        json_encode(
                            array_merge(
                                $data->columns(), 
                                !$slot->isEmpty()
                                    ? ['Actions']
                                    : []
                            )
                        )
                    !!},
                    "data": data,
                },
                columns: [
                    {
                        select: 1,
                        render: function(value, td, rowIndex, cellIndex) {
                            if (!value || !Array.isArray(value) || value.length === 0) return '';
                            const text = value[0]?.data || '';
                            return `<span class="max-h-24 overflow-hidden line-clamp-3">${text.replace(/\n/g, '<br>')}</span>`;
                        }
                    },
                    @if (!$slot->isEmpty())
                    {
                        select: data[0].length - 1,
                        sortable: false,
                        render: function (value, td, rowIndex, cellIndex) {
                            return `{!! $slot !!}`
                        },
                    },
                    @endif
                ],
                labels: {
                    placeholder: "Rechercher...",
                    searchTitle: "Search within table",
                    pageTitle: "Page {page}",
                    perPage: "résultats par page",
                    noRows: "Aucun résultat",
                    info: "{start} - {end} sur {rows} résultats au total",
                    noResults: "Aucun résultat pour votre recherche",
                }
            });
        }
    });
</script>
