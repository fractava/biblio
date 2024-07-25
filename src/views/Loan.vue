<template>
	<div>
		<!-- TRANSLATORS Header of section, that allows loaning out items to customers -->
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
							:options="untilPresetOptions"
							:label="'name'"
							:reduce="reduce" />
					</td>
				</tr>
				<tr v-if="currentLoanUntilPresetId === 'custom'">
				</tr>
				<tr>
					<td>
						<NcTextField :label="t('biblio', 'Barcode')"
							:value.sync="currentBarcode"
							:show-trailing-button="false"
							@keydown.enter.prevent="loan" />
					</td>
				</tr>
				<tr v-for="field in itemInstancesStore.sortedFields">
					<td>
						<FieldValue :is="FieldTypes[field.type].valueEditComponent"
							:field-type="FieldTypes[field.type]"
							:allow-value-edit="true"
							:value="fieldValues[field.id]"
							:settings="field.settings"
							:name="field.name"
							@update:value="(newValue) => { fieldValues[field.id] = newValue }" />
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
import { useItemInstancesStore } from "../store/itemInstances.js";

import FieldTypes from "../models/FieldTypes";

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
			FieldTypes,
			fieldValues: {},
		};
	},
	computed: {
		...mapStores(useBiblioStore, useNomenclatureStore, useItemInstancesStore),
		untilPresetOptions() {
			return [...this.biblioStore.selectedCollectionLoanUntilPresets, {
				id: "custom",
				type: "absolute",
				name: t("biblio", "Select custom date"),
			}]
		},
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
				fieldValues: Object.entries(this.fieldValues).map(([fieldId, value]) => ({ fieldId, value })),
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
		ensureValuesForAllFields() {
			console.log(this.itemInstancesStore?.fields);
			const fieldIds = [];
			// set values for fields not changed yet to default
			for(const field of this.itemInstancesStore.fields) {
				fieldIds.push(field.id.toString());
				if(!this.fieldValues.hasOwnProperty(field.id)) {
					console.log("setting new field value to default: ", field.id, JSON.stringify(this.FieldTypes[field.type].defaultValue));
					this.$set(this.fieldValues, field.id, this.FieldTypes[field.type].defaultValue);
				}
			}

			console.log(fieldIds);

			// remove field values for fields that no longer exist
			for(const fieldId of Object.keys(this.fieldValues)) {
				if(!fieldIds.includes(fieldId)) {
					console.log("deleting old field value: ", fieldId);
					this.$delete(this.fieldValues, fieldId)
				}
			}
		}
	},
	watch: {
		'itemInstancesStore.fields': {
			handler (){
        		this.ensureValuesForAllFields();
			},
			immediate: true
		},
	}
};
</script>
