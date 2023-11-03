<template>
	<div>
		<SectionHeader>{{ t("biblio", "Loan") }}</SectionHeader>
		<vueSelect v-model="currentCustomer"
			style="width: 100%;"
			:options="searchResults"
			:label="'name'"
			:reduce="reduce"
			@search="refreshSearchResults" />
		<NcTextField label="Barcode"
			:value.sync="currentBarcode"
			:show-trailing-button="true"
			trailing-button-icon="arrowRight"
			@trailing-button-click="loan"
			@keydown.enter.prevent="loan" />
	</div>
</template>
<script>
import vueSelect from "vue-select";
import PCancelable from "p-cancelable";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import SectionHeader from "../components/SectionHeader.vue";

import { api } from "../api.js";

export default {
	components: {
		vueSelect,
		NcTextField,
		SectionHeader,
	},
	data() {
		return {
			currentlyRunningFetch: false,
			searchResults: [],
			currentBarcode: "",
			currentCustomer: null,
		};
	},
	mounted() {
		this.refreshSearchResults("", () => {});
	},
	methods: {
		reduce(option) {
			return option.id;
		},
		refreshSearchResults(search, loading) {
			const route = window.biblioRouter.currentRoute;

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newCustomerFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!route.params.collectionId) {
					this.searchResults = [];
					return resolve();
				}

				const filters = {
					name: {
						operator: "contains",
						operand: search,
					},
				};

				loading(true);

				const apiPromise = api.getCustomers(route.params.collectionId, "model", filters, "name", false, 100, 0);

				apiPromise.then((result) => {
					this.searchResults = result.customers;
					loading(false);
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

			newCustomerFetch.catch(() => {});

			this.currentlyRunningFetch = newCustomerFetch;
		},
		loan() {
			const route = window.biblioRouter.currentRoute;

			api.createLoan(route.params.collectionId, {
				barcode: this.currentBarcode,
				customerId: this.currentCustomer,
				until: 10000000,
			})
				.then(() => {
					this.currentBarcode = "";
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not loan item instance"));
				});
		},
	},
};
</script>
