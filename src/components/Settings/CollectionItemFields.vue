<template>
	<div>
		<FieldsTable :fields="fields"
			:fields-order="settingsStore.selectedCollection.itemFieldsOrder"
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
import debounceFn from "debounce-fn";

import { api } from "../../api.js";
import FieldsTable from "../Fields/FieldsTable.vue";
import AddFieldButton from "../Fields/AddFieldButton.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useItemsStore } from "../../store/items.js";
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
		api.getItemFields(this.settingsStore.context?.collectionId)
			.then((fields) => {
				this.fields = fields;
			})
			.catch((error) => {
				console.error(error);
				showError(t("biblio", "Could not fetch item fields"));
			});
	},
	methods: {
		onFieldOrderUpdate(itemFieldsOrder) {
			this.settingsStore.updateSelectedCollection({
				itemFieldsOrder,
			});
		},
		onFieldUpdate(id, parameters) {
			// optimistic update
			const fieldIndex = this.fields.findIndex(field => field.id === id);
			Object.assign(this.fields[fieldIndex], parameters);

			api.updateItemField(this.settingsStore.context?.collectionId, id, parameters)
				.then((updatedField) => {
					// const fieldIndex = this.fields.findIndex(field => field.id === id);
					// Vue.set(this.fields, fieldIndex, updatedField);

					this.refreshItemFieldsInBiblioStoreIfNeeded();
				}).catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not update item field"));
				});
		},

		addField(type, field) {
			api.createItemField(this.settingsStore.context?.collectionId, {
				type,
				name: "",
				settings: field.defaultSettings,
				includeInList: false,
			})
				.then((newField) => {
					this.fields.push(newField);

					this.settingsStore.updateSelectedCollection({
						itemFieldsOrder: [...this.settingsStore.selectedCollection.itemFieldsOrder, newField.id],
					});

					this.refreshItemFieldsInBiblioStoreIfNeeded();
				})
				.catch((error) => {
					console.error(error);
					showError(t("biblio", "Could not create item field"));
				});
		},

		deleteField(itemFieldId) {
			return new Promise((resolve, reject) => {
				api.deleteItemField(this.settingsStore.context?.collectionId, itemFieldId)
					.then(() => {
						this.fields = this.fields.filter(itemField => itemField.id !== itemFieldId);

						this.refreshItemFieldsInBiblioStoreIfNeeded();

						resolve();
					})
					.catch((error) => {
						console.error(error);
						showError(t("biblio", "Could not delete item field"));
						resolve();
					});
			});
		},

		refreshItemFieldsInBiblioStoreIfNeeded: debounceFn(function() {
			const biblioStore = useBiblioStore();
			const itemsStore = useItemsStore();
			const settingsStore = useSettingsStore();

			// the settings made changes to the item fields of the collection currently selected in the main application
			// refresh the data, so the changes take effect in the main application without a manual refresh

			if (biblioStore.selectedCollectionId === settingsStore.context?.collectionId) {
				itemsStore.fetchFields();
			}
		}, { wait: 2000 }),
	},
};
</script>
