import Vue from "vue";
import { defineStore } from "pinia";
import PCancelable from 'p-cancelable';
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

import { useBiblioStore } from "./biblio.js";

export const useCustomersStore = defineStore("customers", {
	state: () => ({
		fields: [],
		searchResults: [],
		searchMeta: {},
		search: "",
		filters: {},
		sort: "name",
		sortReverse: false,
		limit: 100,
		page: 1,
		currentlyRunningFetch: false,
	}),
	actions: {
		/* Customer Fields */
		fetchFields() {
			const biblioStore = useBiblioStore();

			if (!biblioStore.selectedCollectionId) {
				return;
			}

			api.getCustomerFields(biblioStore.selectedCollectionId)
				.then((fields) => {
					this.fields = fields;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch customer fields"));
				});
		},

		/* Customers */
		createCustomer(parameters) {
			const biblioStore = useBiblioStore();

			if (!biblioStore.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.createCustomer(biblioStore.selectedCollectionId, parameters)
					.then((result) => {
						this.searchResults.push(result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not create customer"));
						resolve();
					});
			});
		},
		refreshSearchResults() {
			const biblioStore = useBiblioStore();

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newCustomerFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!biblioStore.selectedCollectionId) {
					this.searchResults = [];
					return resolve();
				}

				const filters = Object.assign({}, this.filters);

				// only fetch customer field values, that will be shown in the customers table
				filters.fieldValues_includeInList = {
					operator: "=",
					operand: true,
				};

				if (this.search) {
					filters.name = {
						operator: "contains",
						operand: this.search,
					};
				}

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getCustomers(biblioStore.selectedCollectionId, "model+fields", filters, this.sort, this.sortReverse, this.limit, offset);

				apiPromise.then((result) => {
					this.searchResults = result.customers;
					this.searchMeta = result.meta;
					resolve();
				})
					.catch((error) => {
						if (!apiPromise.isCanceled) {
							console.error(error);
							showError(t("biblio", "Could not fetch customers"));
						}

						resolve();
					});

				onCancel(() => {
					apiPromise.cancel();
				});
			});

			this.currentlyRunningFetch = newCustomerFetch;

			return newCustomerFetch;
		},
		updateCustomer(customerId, parameters) {
			const biblioStore = useBiblioStore();

			if (!biblioStore.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.updateCustomer(biblioStore.selectedCollectionId, customerId, parameters)
					.then((result) => {
						const updatedIndex = this.searchResults.findIndex(customer => customer.id === customerId);
						Vue.set(this.searchResults, updatedIndex, result);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not update customer"));
						resolve();
					});

				resolve();
			});
		},
		deleteCustomer(customerId) {
			const biblioStore = useBiblioStore();

			if (!biblioStore.selectedCollectionId) {
				return;
			}

			return new Promise((resolve, reject) => {
				api.deleteCustomer(biblioStore.selectedCollectionId, customerId)
					.then((result) => {
						const deletedIndex = this.searchResults.findIndex(customer => customer.id === customerId);
						this.searchResults.splice(deletedIndex, 1);
						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete customer"));
						resolve();
					});

				resolve();
			});
		},
	},
	getters: {
		getCustomerById: (state) => (id) => {
			return state.searchResults.find(customer => customer.id === id);
		},
		sortedFields: (state) => {
			const biblioStore = useBiblioStore();
			return state.fields.toSorted((a, b) => biblioStore.selectedCollection.customerFieldsOrder.indexOf(a.id) - biblioStore.selectedCollection.customerFieldsOrder.indexOf(b.id));
		},
		includedSortedFields: (state) => {
			return state.sortedFields.filter((customerField) => (customerField.includeInList));
		},
	},
});
