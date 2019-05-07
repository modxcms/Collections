export default class Actions {
    constructor(config, fetch, emitter) {
        this.config = config;
        this.fetch = fetch;
        this.emitter = emitter;
    }

    getCollections() {
        return this.fetch(`${this.config.endpoint}?action=get-collections`).then(response => {
            return response.json();
        }).then(data => {
            return data.data.collections;
        });
    }

    getAuthors(collection) {
        return this.fetch(`${this.config.endpoint}?action=get-authors&collection=${collection}`).then(response => {
            return response.json();
        }).then(data => {
            return data.data;
        });
    }

    getCollectionView(collection) {
        return this.fetch(`${this.config.endpoint}?action=get-collection-view&collection=${collection}`).then(response => {
            return response.json();
        }).then(data => {
            return data.data;
        });
    }
}

