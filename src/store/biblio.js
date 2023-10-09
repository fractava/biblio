import Vue from "vue";
import { defineStore } from "pinia";
import PCancelable from 'p-cancelable';
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: false,
		itemFields: [],
		itemSearchResults: [],
		itemSearchMeta: {},
		itemSearch: "",
		itemFilters: {},
		itemSort: "title",
		itemSortReverse: false,
		itemLimit: 100,
		itemPage: 1,
		currentlyRunningItemFetch: false,
	}),
	actions: {
		/* Collections */
		selectCollection(id) {
			this.selectedCollectionId = id;
			this.fetchItemFields();
			this.refreshItemSearchResults();
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
						this.itemSearchResults.push(result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not create item"));
						resolve();
					});
			});
		},
		refreshItemSearchResults() {
			if (this.currentlyRunningItemFetch) {
				this.currentlyRunningItemFetch.cancel();
			}

			const newItemFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!this.selectedCollectionId) {
					this.itemSearchResults = [];
					return resolve();
				}

				const filters = Object.assign({}, this.itemFilters);

				// only fetch item field values, that will be shown in the item table
				filters.fieldValues_includeInList = {
					operator: "=",
					operand: true,
				};

				if (this.itemSearch) {
					filters.title = {
						operator: "contains",
						operand: this.itemSearch,
					};
				}

				const offset = (this.itemPage - 1) * this.itemLimit;

				const apiPromise = api.getItems(this.selectedCollectionId, "model+fields", filters, this.itemSort, this.itemSortReverse, this.itemLimit, offset);

				apiPromise.then((result) => {
					this.itemSearchResults = result.items;
					this.itemSearchMeta = result.meta;
					resolve();
				})
					.catch((error) => {
						if (!apiPromise.isCanceled) {
							console.error(error);
							showError(t("biblio", "Could not fetch items"));
						}

						resolve();
					});

				onCancel(() => {
					apiPromise.cancel();
				});
			});

			this.currentlyRunningItemFetch = newItemFetch;

			return newItemFetch;
		},
		updateItem(itemId, parameters) {
			if (!this.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.updateItem(this.selectedCollectionId, itemId, parameters)
					.then((result) => {
						const updatedIndex = this.itemSearchResults.findIndex(item => item.id === itemId);
						Vue.set(this.itemSearchResults, updatedIndex, result);
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
		deleteItem(itemId) {
			if (!this.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.deleteItem(this.selectedCollectionId, itemId)
					.then((result) => {
						const deletedIndex = this.itemSearchResults.findIndex(item => item.id === itemId);
						this.itemSearchResults.splice(deletedIndex, 1);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete item"));
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
