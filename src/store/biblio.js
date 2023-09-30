import Vue from "vue";
import { defineStore } from "pinia";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: false,
		itemFields: [],
		items: [],
		itemSearch: "",
		itemFilters: {},
	}),
	actions: {
		/* Collections */
		selectCollection(id) {
			this.selectedCollectionId = id;
			this.fetchItemFields();
			this.fetchItems();
		},
		fetchCollections() {
			api.getCollections()
				.then((result) => {
					this.collections = result;
				}).catch(() => {
					showError(t("biblio", "Could not fetch collections"));
				});
		},
		createCollection(parameters) {
			return new Promise((resolve, reject) => {
				api.createCollection(parameters)
					.then((result) => {
						this.collections.push(result);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not create collection"));
						resolve();
					});
			});
		},
		updateCollection(id, parameters) {
			return new Promise((resolve, reject) => {
				api.updateCollection(id, parameters)
					.then((result) => {
						const updatedIndex = this.collections.findIndex(collection => collection.id === id);
						Vue.set(this.collections, updatedIndex, result);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not update collection"));
						resolve();
					});
			});
		},
		deleteCollection(id) {
			return new Promise((resolve, reject) => {
				api.deleteCollection(id)
					.then(() => {
						this.collections = this.collections.filter(collection => collection.id !== id);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not delete collection"));
						resolve();
					});
			});
		},

		/* Item Fields */
		fetchItemFields() {
			if (!this.selectedCollectionId) {
				return;
			}

			api.getItemFields(this.selectedCollectionId)
				.then((fields) => {
					this.itemFields = fields;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch item fields"));
				});
		},

		/* Items */
		createItem(parameters) {
			if (!this.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.createItem(this.selectedCollectionId, parameters)
					.then((result) => {
						this.items.push(result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not create item"));
						resolve();
					});
			});
		},
		fetchItems() {
			return new Promise((resolve, reject) => {
				if (!this.selectedCollectionId) {
					this.items = [];
					resolve();
				}
				api.getItems(this.selectedCollectionId, "model+fields")
					.then((result) => {
						this.items = result;
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not fetch items"));
						resolve();
					});
			});
		},
		updateItem(itemId, parameters) {
			if (!this.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.updateItem(this.selectedCollectionId, itemId, parameters)
					.then((result) => {
						const updatedIndex = this.items.findIndex(item => item.id === itemId);
						Vue.set(this.items, updatedIndex, result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not update item"));
						resolve();
					});

				resolve();
			});
		},
	},
	getters: {
		getItemById: (state) => (id) => {
			return state.items.find(item => item.id == id);
		},
	},
});
