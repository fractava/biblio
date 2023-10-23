import Vue from "vue";
import { defineStore } from "pinia";
import PCancelable from 'p-cancelable';
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

import { useBiblioStore } from "./biblio.js";

export const useItemInstancesStore = defineStore("itemInstances", {
	state: () => ({
		fields: [],
		searchResults: [],
		searchMeta: {},
		search: "",
		filters: {},
		sort: "barcode",
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

			api.getLoanFields(route.params.collectionId)
				.then((fields) => {
					this.fields = fields;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch loan fields"));
				});
		},

		/* Item Instances */
		refreshSearchResults() {
			const route = window.biblioRouter.currentRoute;

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newItemInstancesFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!route.params.collectionId) {
					this.searchResults = [];
					return resolve();
				}

				const filters = Object.assign({}, this.filters);

				if (this.search) {
					filters.barcode = {
						operator: "contains",
						operand: this.search,
					};
				}

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getItemInstances(route.params.collectionId, "model+item+loan+loan_customer+fields", filters, this.sort, this.sortReverse, this.limit, offset);

				apiPromise.then((result) => {
					this.searchResults = result.instances;
					this.searchMeta = result.meta;
					resolve();
				})
					.catch((error) => {
						if (!apiPromise.isCanceled) {
							console.error(error);
							showError(t("biblio", "Could not fetch item instances"));
						}

						resolve();
					});

				onCancel(() => {
					apiPromise.cancel();
				});
			});

			this.currentlyRunningFetch = newItemInstancesFetch;

			return newItemInstancesFetch;
		},
		deleteItemInstance(itemId) {
			const route = window.biblioRouter.currentRoute;

			if (!route.params.collectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.deleteItemInstance(route.params.collectionId, itemId)
					.then((result) => {
						const deletedIndex = this.searchResults.findIndex(item => item.id === itemId);
						this.searchResults.splice(deletedIndex, 1);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete item instance"));
						resolve();
					});

				resolve();
			});
		},
	},
	getters: {
		getItemInstanceById: (state) => (id) => {
			return state.searchResults.find(instance => instance.id === id);
		},
		sortedFields: (state) => {
			const biblioStore = useBiblioStore();
			return state.fields.toSorted((a, b) => biblioStore.selectedCollection?.loanFieldsOrder?.indexOf(a.id) - biblioStore.selectedCollection?.loanFieldsOrder?.indexOf(b.id));
		},
	},
});
