import Actions from './Actions';
import Tabulator from 'tabulator-tables';
import {debounce} from './Utils';

export default config => (fred, Plugin, pluginTools) => {
    const {dl, dt, table, tr, th, td, a, div, button} = pluginTools.ui.els;
    const {text, select} = pluginTools.ui.ins;
    const {publishResource, unpublishResource, deleteResource, undeleteResource} =  pluginTools.actions.pages;

    return class SidebarPlugin extends Plugin {
        static title = 'Collections';
        static icon = 'fred--collections-sidebar';
        static expandable = true;

        init() {
            this.actions = new Actions(config, pluginTools.fetch, pluginTools.emitter);
        }

        click() {
            return this.actions.getCollections().then(collections => this.render(collections));
        }

        render(collections) {
            const list = dl();

            collections.forEach(collection => {
                const link = dt(collection.pagetitle, [], () => {
                    pluginTools.emitter.emit('fred-loading', 'Loading collection');
                    this.actions.getAuthors(collection.id).then(authors => {
                        this.renderModal(collection, authors);
                        pluginTools.emitter.emit('fred-loading-hide');
                    });


                    // pluginTools.emitter.emit('fred-loading', 'Loading collection');
                    //
                    // this.actions.getCollection(collection.id, 2).then(data => {
                    //     this.renderModal(collection, data);
                    //     pluginTools.emitter.emit('fred-loading-hide');
                    // });
                });

                list.appendChild(link);
            });




            return list;
        }

        renderModal(collection, authors) {
            const filtersBar = div();
            const tableWrapper = div();

            const table = new Tabulator(tableWrapper, {
                ajaxURL: config.endpoint,
                ajaxParams: {
                    action: 'get-collection',
                    collection: collection.id
                },
                ajaxConfig: {
                    credentials: 'same-origin',
                    headers: {
                        'X-Fred-Token': pluginTools.fredConfig.jwt
                    }
                },
                ajaxSorting:true,
                columnHeaderSortMulti:false,
                pagination:"remote",
                paginationSize:5,
                ajaxFiltering:true,
                layout:"fitColumns",
                columns:[
                    {title:"Pagetitle", field:"pagetitle", formatter:function(cell, formatterParams, onRendered){
                            const data = cell.getRow().getData();

                            return cell.getValue() + `<br><a href="${data.fullUrl}">${data.url}</a>`;
                        }},
                    {title:"Published", field:"publishedon_combined", width: 120},
                    {title:"Expires", field:"unpub_date", width: 120},
                    {title:"Author", field:"fullname", headerSort:false, width: 180},
                    {
                        title:"Actions",
                        headerSort:false,
                        formatter: function(cell, formatterParams, onRendered){
                            const data = cell.getRow().getData();

                            const edit = (data.deleted) ? '' : `<a href="${data.fullUrl}" class="fred--btn fred--btn-collections-icon fred--btn-collections-edit" alt="Edit" title="Edit">E</a>`;
                            const publish = (data.published) ? '<button data-action=\'unpublish\' class="fred--btn fred--btn-collections-icon fred--btn-collections-unpublish" alt="Unpublish" title="Unpublish">UP</button>' : '<button data-action=\'publish\' class="fred--btn fred--btn-collections-icon fred--btn-collections-publish" alt="Publish" title="Publish">P</button>';
                            const deleteAction = (data.deleted) ? '<button data-action=\'undelete\' class="fred--btn fred--btn-collections-icon fred--btn-collections-undelete" alt="Undelete" title="Undelete">UD</button>' : '<button data-action=\'delete\' class="fred--btn fred--btn-collections-icon fred--btn-collections-delete" alt="Delete" title="Delete">D</button>';

                            return `${edit} ${publish} ${deleteAction}`;
                        },
                        align:"left", cellClick: function(e, cell){
                            if (e.target.dataset.action) {
                                switch (e.target.dataset.action) {
                                    case 'publish':
                                        publishResource(cell.getRow().getData().id).then(() => {
                                            table.setPage(table.getPage());
                                        });
                                        break;
                                    case 'unpublish':
                                        unpublishResource(cell.getRow().getData().id).then(() => {
                                            table.setPage(table.getPage());
                                        });
                                        break;
                                    case 'delete':
                                        deleteResource(cell.getRow().getData().id).then(() => {
                                            table.setPage(table.getPage());
                                        });
                                        break;
                                    case 'undelete':
                                        undeleteResource(cell.getRow().getData().id).then(() => {
                                            table.setPage(table.getPage());
                                        });
                                        break;
                                }
                            }
                        },
                        width: 120
                    }
                ]
            });

            const filters = {
                query: '',
                published: '-1',
                createdby: '-1'
            };

            const filter = (name, value) => {
                filters[name] = value;
                let normalizedFilters = [];

                for (let fieldName in filters) {
                    if (filters.hasOwnProperty(fieldName)) {
                        normalizedFilters.push({
                            field: fieldName,
                            type: '=',
                            value: filters[fieldName]
                        });
                    }
                }

                table.setFilter(normalizedFilters);
            };

            const debouncedFilter = debounce(200, filter);

            const search = text({
                name: 'query',
                label: 'Search',
                labelAsPlaceholder: true
            }, '', debouncedFilter);

            const publishedFilter = select({
                name: 'published',
                label: 'Published',
                options: {'-1': 'Any', '1': 'Published', '0': 'Unpublished'}
            }, '-1', debouncedFilter);

            const authorFilter = select({
                name: 'createdby',
                label: 'Author',
                options: authors
            }, '-1', debouncedFilter);

            const newPage = button('New Page', 'New Page', ['fred--btn','fred--btn-collections-newpage'], () => console.log('New Page'));

            filtersBar.appendChild(search);
            filtersBar.appendChild(publishedFilter);
            filtersBar.appendChild(authorFilter);
            filtersBar.appendChild(newPage);

            const modalContent = div();
            modalContent.appendChild(filtersBar);
            modalContent.appendChild(tableWrapper);

            const modal = new pluginTools.Modal(collection.pagetitle, modalContent, null, {
                showCancelButton: false,
                showSaveButton: false,
                width: '1000px'
            });

            modal.render();
        }

    }
}
