import Actions from './Actions';
import Tabulator from 'tabulator-tables';
import {debounce} from './Utils';

export default config => (fred, Plugin, pluginTools) => {
    const {dl, dt, div, button, form, fieldSet} = pluginTools.ui.els;
    const {text, select, choices} = pluginTools.ui.ins;
    const {publishResource, unpublishResource, deleteResource, undeleteResource, createResource, getTemplates} = pluginTools.actions.pages;
    const {getBlueprints} = pluginTools.actions.blueprints;
    const {emitter, fetch} = pluginTools;

    return class SidebarPlugin extends Plugin {
        static title = 'collections.fred.collections';
        static icon = 'fred--collections-sidebar';
        static expandable = true;

        init() {
            this.actions = new Actions(config, fetch, emitter);
        }

        click() {
            return this.actions.getCollections().then(collections => this.render(collections));
        }

        render(collections) {
            const list = dl();

            collections.forEach(collection => {
                const link = dt(collection.pagetitle, [], () => {
                    pluginTools.emitter.emit('fred-loading', 'collections.fred.loading_colleciton');
                    Promise.all([this.actions.getCollectionView(collection.id), this.actions.getAuthors(collection.id)]).then(values => {
                        this.renderModal(collection, ...values);
                        pluginTools.emitter.emit('fred-loading-hide');
                    });
                });

                list.appendChild(link);
            });

            return list;
        }

        renderModal(collection, view, authors) {
            const filtersBar = div(['fred--collections-filters']);
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
                ajaxSorting: true,
                columnHeaderSortMulti: false,
                pagination: "remote",
                paginationSize: 5,
                ajaxFiltering: true,
                responsiveLayout: "hide",
                layout: "fitColumns",
                columns: [
                    {
                        title: pluginTools.fredConfig.lng("collections.fred.pagetitle"),
                        field: "pagetitle",
                        responsive: 0,
                        formatter: (cell, formatterParams, onRendered) => {
                            const data = cell.getRow().getData();

                            return cell.getValue() + `<br><a href="${data.fullUrl}">${data.url}</a>`;
                        }
                    },
                    {
                        title: pluginTools.fredConfig.lng("collections.fred.publish"),
                        field: "publishedon_combined",
                        width: 120,
                        responsive: 2,
                        widthShrink: 1
                    },
                    {
                        title: pluginTools.fredConfig.lng("collections.fred.expires"),
                        field: "unpub_date",
                        width: 120,
                        responsive: 4,
                        widthShrink: 1
                    },
                    {
                        title: pluginTools.fredConfig.lng("collections.fred.author"),
                        field: "fullname",
                        headerSort: false,
                        width: 180,
                        responsive: 3,
                        widthShrink: 1
                    },
                    {
                        title: pluginTools.fredConfig.lng("collections.fred.actions"),
                        headerSort: false,
                        formatter: (cell, formatterParams, onRendered) => {
                            const data = cell.getRow().getData();

                            const edit = (data.deleted) ? '' : `<a href="${data.fullUrl}" class="fred--btn fred--btn-collections-icon fred--btn-collections-edit" title="${pluginTools.fredConfig.lng('collections.fred.edit')}"></a>`;
                            const publish = (data.published) ? (pluginTools.fredConfig.permission.unpublish_document ? `<button data-action='unpublish' class="fred--btn fred--btn-collections-icon fred--btn-collections-unpublish" title="${pluginTools.fredConfig.lng('collections.fred.unpublish')}"></button>` : '') : (pluginTools.fredConfig.permission.publish_document ? `<button data-action='publish' class="fred--btn fred--btn-collections-icon fred--btn-collections-publish" title="${pluginTools.fredConfig.lng('collections.fred.publish')}"></button>` : '');
                            const deleteAction = (data.deleted) ? (pluginTools.fredConfig.permission.undelete_document ? `<button data-action='undelete' class="fred--btn fred--btn-collections-icon fred--btn-collections-undelete" title="${pluginTools.fredConfig.lng('collections.fred.undelete')}"></button>` : '') : (pluginTools.fredConfig.permission.delete_document ? `<button data-action='delete' class="fred--btn fred--btn-collections-icon fred--btn-collections-delete" title="${pluginTools.fredConfig.lng('collections.fred.delete')}"></button>` : '');

                            return `${edit} ${publish} ${deleteAction}`;
                        },
                        align: "left",
                        cellClick: (e, cell) => {
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
                label: 'collections.fred.search',
            }, '', debouncedFilter);

            const publishedFilter = select({
                name: 'published',
                label: 'collections.fred.published',
                options: {'-1': pluginTools.fredConfig.lng('collections.fred.any'), '1': pluginTools.fredConfig.lng('collections.fred.published'), '0': pluginTools.fredConfig.lng('collections.fred.unpublished')}
            }, '-1', debouncedFilter);

            const authorFilter = select({
                name: 'createdby',
                label: 'collections.fred.author',
                options: authors
            }, '-1', debouncedFilter);


            filtersBar.appendChild(search);
            filtersBar.appendChild(publishedFilter);
            filtersBar.appendChild(authorFilter);

            if (pluginTools.fredConfig.permission.new_document) {
                const newPage = button('collections.fred.new_page', 'collections.fred.new_page', ['fred--btn', 'fred--btn-collections-newpage'], () => {
                    const newPageForm = this.showNewPageForm(collection, view, () => {
                        filtersBar.replaceChild(newPage, newPageForm);
                    });
                    filtersBar.replaceChild(newPageForm, newPage);
                });
                filtersBar.appendChild(newPage);
            }

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

        showNewPageForm(collection, view, onCancel) {
            const formWrapper = div();

            const pageForm = form(['fred--pages_create']);

            const fields = fieldSet();

            const state = {
                pagetitle: '',
                parent: collection.id,
                blueprint: 0,
                template: view.template,
                theme: pluginTools.fredConfig.config.theme
            };

            const onChange = (name, value) => {
                state[name] = value;
            };

            const onChangeChoices = (name, value) => {
                if ((name === 'template') && value.customProperties && value.customProperties.theme) {
                    state.theme = value.customProperties.theme;

                    blueprintInput.choices.clearStore();
                    blueprintInput.choices.ajax(callback => {
                        getBlueprints(true, state.theme)
                            .then(categories => {
                                const groups = [];

                                categories.forEach(category => {
                                    const options = [];

                                    category.blueprints.forEach(blueprint => {
                                        const blueprintOption = {
                                            label: blueprint.name,
                                            value: '' + blueprint.id
                                        };

                                        if (value.customProperties.default_blueprint && (blueprint.id === value.customProperties.default_blueprint)) {
                                            blueprintOption.selected = true;
                                            state.blueprint = blueprint.id;
                                        }

                                        options.push(blueprintOption);
                                    });

                                    groups.push({
                                        label: category.category,
                                        disabled: false,
                                        choices: options
                                    });
                                });

                                callback(groups, 'value', 'label');
                            })
                            .catch(error => {
                                emitter.emit('fred-loading', error.message);
                            });
                    });
                }

                state[name] = value.value;
            };

            const blueprintInput = choices({
                name: 'blueprint',
                label: pluginTools.fredConfig.lng('fred.fe.pages.blueprint'),
                choices: {
                    removeItemButton: true
                }
            }, state.blueprint, onChangeChoices, (setting, labelEl, selectEl, choicesInstance) => {
                choicesInstance.passedElement.addEventListener('removeItem', event => {
                    const value = choicesInstance.getValue(false);
                    if (value === undefined) {
                        state.blueprint = '0';
                    }
                });
            });

            const pagetitle = text({
                name: 'pagetitle',
                label: 'fred.fe.pages.page_title'
            }, state.pagetitle, onChange);

            fields.appendChild(pagetitle);

            const templateInput = choices({
                name: 'template',
                label: pluginTools.fredConfig.lng('fred.fe.pages.template'),
            }, state.parent, onChangeChoices, (setting, label, select, choicesInstance, defaultValue) => {
                choicesInstance.ajax(callback => {
                    getTemplates()
                        .then(data => {
                            if (data.data.templates[0]) {
                                onChangeChoices('template', data.data.templates[0]);
                                data.data.templates[0].selected = true;
                            }
                            callback(data.data.templates, 'value', 'name');
                        })
                        .catch(error => {
                            emitter.emit('fred-loading', error.message);
                        });
                });
            });

            fields.appendChild(templateInput);

            fields.appendChild(blueprintInput);

            const createButton = button('fred.fe.pages.create_page', 'fred.fe.pages.create_page', ['fred--btn-panel', 'fred--btn-apply'], () => {
                if (!pluginTools.fredConfig.permission.new_document) {
                    alert(pluginTools.fredConfig.lng('fred.fe.permission.new_document'));
                    return;
                }

                if (!state.parent === 0 && !pluginTools.fredConfig.permission.new_document_in_root) {
                    alert(pluginTools.fredConfig.lng('fred.fe.permission.new_document_in_root'));
                    return;
                }

                emitter.emit('fred-loading', pluginTools.fredConfig.lng('fred.fe.pages.creating_page'));

                createResource(state.parent, state.template, state.pagetitle, state.blueprint)
                    .then(json => {
                        location.href = json.url;
                        emitter.emit('fred-loading-hide');
                    }).catch(err => {
                        if (err.response._fields.pagetitle) {
                            pagetitle.onError(err.response._fields.pagetitle);
                        }

                        emitter.emit('fred-loading-hide');
                        return false;
                    }
                );
            });

            const cancelButton = button('collections.fred.cancel', 'collections.fred.cancel', ['fred--btn-panel', 'fred--btn-danger'], () => {
                onCancel();
            });

            fields.appendChild(createButton);
            fields.appendChild(cancelButton);

            pageForm.appendChild(fields);

            formWrapper.appendChild(pageForm);

            return formWrapper;
        }

    }
}
