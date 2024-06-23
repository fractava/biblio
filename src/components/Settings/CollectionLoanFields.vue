<template>
	<div>
		<FieldsTable :fields="fields"
			:fields-order="settingsStore.selectedCollection.loanFieldsOrder"
			@update:fieldsOrder="onFieldOrderUpdate"
			@field-update="onFieldUpdate"
			@delete="deleteField" />

		<AddFieldButton @add-field="addField" />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import { showError } from "@nextcloud/dialogs";
import debounceFn from "debounce-fn";

import { api } from "../../api.js";
import FieldsTable from "../Fields/FieldsTable.vue";
import AddFieldButton from "../Fields/AddFieldButton.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useItemInstancesStore } from "../../store/itemInstances.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		FieldsTable,
		AddFieldButton,
	},
	data() {
		return {
			fields: [],
		};
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	mounted() {
		api.getLoanFields(this.settingsStore.context?.collectionId)
			.then((fields) => {
				this.fields = fields;
			})
			.catch((error) => {
				console.error(error);
				showError(t("biblio", "Could not fetch loan fields"));
			});
	},
	methods: {
		onFieldOrderUpdate(loanFieldsOrder) {
			this.settingsStore.updateSelectedCollection({
				loanFieldsOrder,
			});
		},
		onFieldUpdate(id, parameters) {
			// optimistic update
			const fieldIndex = this.fields.findIndex(field => field.id === id);
			Object.assign(this.fields[fieldIndex], parameters);

			api.updateLoanField(this.settingsStore.context?.collectionId, id, parameters)
				.then((updatedField) => {
					// const fieldIndex = this.fields.findIndex(field => field.id === id);
					// Vue.set(this.fields, fieldIndex, updatedField);

					this.refreshLoanFieldsInBiblioStoreIfNeeded();
				}).catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not update loan field"));
				});
		},

		addField(type, field) {
			api.createLoanField(this.settingsStore.context?.collectionId, {
				type,
				name: "",
				settings: field.defaultSettings,
				includeInList: false,
			})
				.then((newField) => {
					this.fields.push(newField);

					this.settingsStore.updateSelectedCollection({
						loanFieldsOrder: [...this.settingsStore.selectedCollection.loanFieldsOrder, newField.id],
					});

					this.refreshLoanFieldsInBiblioStoreIfNeeded();
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not create loan field"));
				});
		},

		deleteField(loanFieldId) {
			return new Promise((resolve, reject) => {
				api.deleteLoanField(this.settingsStore.context?.collectionId, loanFieldId)
					.then(() => {
						this.fields = this.fields.filter(loanField => loanField.id !== loanFieldId);

						this.refreshLoanFieldsInBiblioStoreIfNeeded();

						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete loan field"));
						resolve();
					});
			});
		},

		refreshLoanFieldsInBiblioStoreIfNeeded: debounceFn(function() {
			const biblioStore = useBiblioStore();
			const itemInstancesStore = useItemInstancesStore();
			const settingsStore = useSettingsStore();

			// the settings made changes to the loan fields of the collection currently selected in the main application
			// refresh the data, so the changes take effect in the main application without a manual refresh

			if (biblioStore.selectedCollectionId === settingsStore.context?.collectionId) {
				itemInstancesStore.fetchFields();
			}
		}, { wait: 2000 }),
	},
};
</script>
