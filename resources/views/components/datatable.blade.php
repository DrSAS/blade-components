@props([
    'pagination' => true,
    'searchable' => true,
    'data' => [],
])

<table id="datatable-{{ $uid }}"></table>

{{-- {{ dd(json_encode(array_map('array_values', array_column($data, 'values')))) }} --}}

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let uid = "datatable-{{ $uid }}"
        if (document.getElementById(uid) && typeof DataTable !== 'undefined') {
            const data = {!! json_encode(array_map('array_values', array_column($data, 'values'))) !!}.map(row => [...row, ''])
            const itemIds = {!! json_encode(array_column($data, 'entityId')) !!}
            const dataTable = new DataTable(`#${uid}`, {
                paging: {{ json_encode($pagination) }},
                searchable: {{ json_encode($searchable) }},
                data: {
                    "headings": {!! json_encode(array_merge(array_keys($data[0]['values']), ['Actions'])) !!},
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
                    {
                        select: data[0].length - 1,
                        sortable: false,
                        render: function (value, td, rowIndex, cellIndex) {
                            return `<a href="${itemIds[rowIndex]}" class="block p-2 bg-blue-800 text-white rounded hover:bg-white hover:text-sky-700 transition-all hover:shadow-black/30 hover:box-shadow-sharp-bottom-2 hover:cursor-pointer">Évaluer</button>`
                        },
                    }
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
