import Vue from "vue";
import { defineStore } from "pinia";
import PCancelable from 'p-cancelable';
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

import { useBiblioStore } from "./biblio.js";

export const useItemsStore = defineStore("items", {
	state: () => ({
		fields: [],
		searchResults: [],
		searchMeta: {},
		search: "",
		filters: {},
		sort: "title",
		sortReverse: false,
		limit: 100,
		page: 1,
		currentlyRunningFetch: false,
	}),
	actions: {
		/* Item Fields */
		fetchFields() {
			const route = window.biblioRouter.currentRoute;

			if (!route.params.collectionId) {
				return;
			}

			api.getItemFields(route.params.collectionId)
				.then((fields) => {
					this.fields = fields;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch item fields"));
				});
		},

		/* Items */
		createItem(parameters) {
			const route = window.biblioRouter.currentRoute;

			if (!route.params.collectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.createItem(route.params.collectionId, parameters)
					.then((result) => {
						this.searchResults.push(result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not create item"));
						resolve();
					});
			});
		},
		refreshSearchResults() {
			const route = window.biblioRouter.currentRoute;

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newItemFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!route.params.collectionId) {
					this.searchResults = [];
					return resolve();
				}

				const filters = Object.assign({}, this.filters);

				// only fetch item field values, that will be shown in the item table
				filters.fieldValues_includeInList = {
					operator: "=",
					operand: true,
				};

				if (this.search) {
					filters.title = {
						operator: "contains",
						operand: this.search,
					};
				}

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getItems(route.params.collectionId, "model+fields", filters, this.sort, this.sortReverse, this.limit, offset);

				apiPromise.then((result) => {
					this.searchResults = result.items;
					this.searchMeta = result.meta;
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

			this.currentlyRunningFetch = newItemFetch;

			return newItemFetch;
		},
		updateItem(itemId, parameters) {
			const route = window.biblioRouter.currentRoute;

			if (!route.params.collectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.updateItem(route.params.collectionId, itemId, parameters)
					.then((result) => {
						const updatedIndex = this.searchResults.findIndex(item => item.id === itemId);
						Vue.set(this.searchResults, updatedIndex, result);
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
			const route = window.biblioRouter.currentRoute;

			if (!route.params.collectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.deleteItem(route.params.collectionId, itemId)
					.then((result) => {
						const deletedIndex = this.searchResults.findIndex(item => item.id === itemId);
						this.searchResults.splice(deletedIndex, 1);
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
			return state.searchResults.find(item => item.id === id);
		},
		sortedFields: (state) => {
			const biblioStore = useBiblioStore();
			return state.fields.toSorted((a, b) => biblioStore.selectedCollection.itemFieldsOrder.indexOf(a.id) - biblioStore.selectedCollection.itemFieldsOrder.indexOf(b.id));
		},
		includedSortedFields: (state) => {
			return state.sortedFields.filter((itemField) => (itemField.includeInList));
		},
	},
});
