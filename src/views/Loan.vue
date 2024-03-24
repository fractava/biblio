<template>
	<div>
		<SectionHeader>{{ t("biblio", "Loan") }}</SectionHeader>
		<table class="loanTable">
			<tbody>
				<tr>
					<td>{{ nomenclatureStore.customer }}</td>
					<td>
						<vueSelect v-model="currentCustomer"
							style="width: 100%;"
							:options="searchResults"
							:label="'name'"
							:reduce="reduce"
							@search="refreshSearchResults" />
					</td>
				</tr>
				<tr>
					<td>{{ t("biblio", "Barcode") }}</td>
					<td>
						<NcTextField label="Barcode"
							:value.sync="currentBarcode"
							:show-trailing-button="false"
							@keydown.enter.prevent="loan" />
					</td>
				</tr>
				<tr>
					<td>{{ t("biblio", "Until") }}</td>
					<td>
						<vueSelect v-model="currentLoanUntilPresetId"
							style="width: 100%;"
							:options="biblioStore.selectedCollectionLoanUntilPresets"
							:label="'name'"
							:reduce="reduce" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>
<script>
import vueSelect from "vue-select";
import PCancelable from "p-cancelable";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";
import { mapStores } from "pinia";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import SectionHeader from "../components/SectionHeader.vue";

import { api } from "../api.js";
import { useBiblioStore } from "../store/biblio.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

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
			currentLoanUntilPresetId: null,
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

			if (!this.currentLoanUntilPreset) {
				return;
			} else {
				if (this.currentLoanUntilPreset.type === "relative") {
					var loanUntil = Math.floor(Date.now() / 1000) + this.currentLoanUntilPreset.timestamp;
				} else {
					var loanUntil = this.currentLoanUntilPreset.timestamp;
				}
			}

			api.createLoan(route.params.collectionId, {
				barcode: this.currentBarcode,
				customerId: this.currentCustomer,
				until: loanUntil,
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
<style lang="scss">
.loanTable {
	width: 100%;
	border-collapse: collapse;

	td, th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
		border-top: 1px solid var(--color-border);
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
</style>
