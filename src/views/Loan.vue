<template>
	<div>
		<SectionHeader>{{ t("biblio", "Loan") }}</SectionHeader>
		<SimpleTable>
			<tbody>
				<tr>
					<td>
						<vueSelect v-model="currentCustomer"
							:placeholder="nomenclatureStore.customer"
							style="width: 100%;"
							:options="searchResults"
							:label="'name'"
							:reduce="reduce"
							@search="refreshSearchResults" />
					</td>
				</tr>
				<tr>
					<td>
						<vueSelect v-model="currentLoanUntilPresetId"
							:placeholder="t('biblio', 'Until')"
							style="width: 100%;"
							:options="biblioStore.selectedCollectionLoanUntilPresets"
							:label="'name'"
							:reduce="reduce" />
					</td>
				</tr>
				<tr>
					<td>
						<NcTextField :label="t('biblio', 'Barcode')"
							:value.sync="currentBarcode"
							:show-trailing-button="false"
							@keydown.enter.prevent="loan" />
					</td>
				</tr>
			</tbody>
		</SimpleTable>
		<SimpleTableSubmitButon :loading="loading" @click="loan" />
	</div>
</template>
<script>
import vueSelect from "vue-select";
import PCancelable from "p-cancelable";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";
import { mapStores } from "pinia";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import SectionHeader from "../components/SectionHeader.vue";
import SimpleTable from "../components/SimpleTable.vue";
import SimpleTableSubmitButon from "../components/SImpleTableSubmitButton.vue";

import { api } from "../api.js";
import { useBiblioStore } from "../store/biblio.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

export default {
	components: {
		vueSelect,
		NcTextField,
		SectionHeader,
		SimpleTable,
		SimpleTableSubmitButon,
	},
	data() {
		return {
			currentlyRunningFetch: false,
			searchResults: [],
			currentBarcode: "",
			currentCustomer: null,
			currentLoanUntilPresetId: null,
			loading: false,
		};
	},
	computed: {
		...mapStores(useBiblioStore, useNomenclatureStore),
		currentLoanUntilPreset() {
			return this.biblioStore.selectedCollectionLoanUntilPresets.find((preset) => (preset.id === this.currentLoanUntilPresetId));
		},
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

			if (!this.currentCustomer) {
				return;
			}

			if (!this.currentBarcode) {
				return;
			}

			let loanUntil;

			if (!this.currentLoanUntilPreset) {
				return;
			} else {
				if (this.currentLoanUntilPreset.type === "relative") {
					loanUntil = Math.floor(Date.now() / 1000) + this.currentLoanUntilPreset.timestamp;
				} else {
					loanUntil = this.currentLoanUntilPreset.timestamp;
				}
			}

			this.loading = true;

			api.createLoan(route.params.collectionId, {
				barcode: this.currentBarcode,
				customerId: this.currentCustomer,
				until: loanUntil,
				fieldValues: [
					{
						fieldId: 2,
						value: "true",
					}
				]
			})
				.then(() => {
					this.currentBarcode = "";
					this.loading = false;
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not loan item instance"));
					this.loading = false;
				});
		},
	},
};
</script>
