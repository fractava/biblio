<template>
	<div>
		<FieldsTable :fields="fields"
			:fields-order="settingsStore.selectedCollection.customerFieldsOrder"
			@update:fieldsOrder="onFieldOrderUpdate"
			@field-update="onFieldUpdate"
			@delete="deleteField" />

		<AddFieldButton @add-field="addField" />
	</div>
</template>

<script>
import Vue from "vue";
import { mapStores } from "pinia";
import { showError } from "@nextcloud/dialogs";
import { debounce } from "debounce";

import { api } from "../../api.js";
import FieldsTable from "../Fields/FieldsTable.vue";
import AddFieldButton from "../Fields/AddFieldButton.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useCustomersStore } from "../../store/customers.js";
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
		api.getCustomerFields(this.settingsStore.context?.collectionId)
			.then((fields) => {
				this.fields = fields;
			})
			.catch((error) => {
				console.error(error);
				showError(t("biblio", "Could not fetch customer fields"));
			});
	},
	methods: {
		onFieldOrderUpdate(customerFieldsOrder) {
			this.settingsStore.updateSelectedCollection({
				customerFieldsOrder,
			});
		},
		onFieldUpdate(id, parameters) {
			// optimistic update
			const fieldIndex = this.fields.findIndex(field => field.id === id);
			Object.assign(this.fields[fieldIndex], parameters);

			api.updateCustomerField(this.settingsStore.context?.collectionId, id, parameters)
				.then((updatedField) => {
					// const fieldIndex = this.fields.findIndex(field => field.id === id);
					// Vue.set(this.fields, fieldIndex, updatedField);

					this.refreshCustomerFieldsInBiblioStoreIfNeeded();
				}).catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not update customer field"));
				});
		},

		addField(type, field) {
			api.createCustomerField(this.settingsStore.context?.collectionId, {
				type,
				name: "",
				settings: field.defaultSettings,
				includeInList: false,
			})
				.then((newField) => {
					this.fields.push(newField);

					this.settingsStore.updateSelectedCollection({
						customerFieldsOrder: [...this.settingsStore.selectedCollection.customerFieldsOrder, newField.id],
					});

					this.refreshCustomerFieldsInBiblioStoreIfNeeded();
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not create customer field"));
				});
		},

		deleteField(customerFieldId) {
			return new Promise((resolve, reject) => {
				api.deleteCustomerField(this.settingsStore.context?.collectionId, customerFieldId)
					.then(() => {
						this.fields = this.fields.filter(field => field.id !== customerFieldId);

						this.refreshCustomerFieldsInBiblioStoreIfNeeded();

						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete customer field"));
						resolve();
					});
			});
		},

		refreshCustomerFieldsInBiblioStoreIfNeeded: debounce(() => {
			const customersStore = useCustomersStore();
			const settingsStore = useSettingsStore();

			// the settings made changes to the customer fields of the collection currently selected in the main application
			// refresh the data, so the changes take effect in the main application without a manual refresh

			if (window.biblioRouter.currentRoute.params.collectionId === settingsStore.context?.collectionId) {
				customersStore.fetchFields();
			}
		}, 2000),
	},
};
</script>
