<template>
	<div>
		<FieldsTable>
			<Draggable :value="fields"
				drag-class="drag"
				ghost-class="ghost"
				:animation="200"
				tag="tbody"
				draggable=".draggableitem"
				handle=".drag-handle-active"
				@input="onFieldsUpdate"
				@start="isDragging = true"
				@end="isDragging = false"
				@change="fieldsOrderChanged">
				<Field v-for="field in fields"
					:key="field.id"
					:name="field.name"
					:include-in-list="!!field.includeInList"
					class="draggableitem"
					@update:name="(newName) => onFieldUpdate(field.id, {name: newName})"
					@update:includeInList="(newIncludeInList) => onFieldUpdate(field.id, {includeInList: newIncludeInList})"
					@delete="deleteField(field)">
					<Icon :is="FieldTypes[field.type].iconComponent" slot="icon" v-tooltip="FieldTypes[field.type].label" />
					<FieldSettings :is="FieldTypes[field.type].settingsComponent"
						slot="settings"
						:settings="field.settings"
						@update:settings="(newSettings) => onFieldUpdate(field.id, {settings: newSettings})" />
				</Field>
			</Draggable>
		</FieldsTable>
		<NcActions class="addFieldButton"
			:open.sync="addFieldMenuOpened"
			:menu-title="t('biblio', 'Add a field')"
			default-icon="icon-add">
			<NcActionButton v-for="(field, type) in FieldTypes"
				:key="field.label"
				:close-after-click="true"
				:icon="field.icon"
				@click="addField(type, field)">
				{{ field.label }}
				<template #icon>
					<Icon :is="field.iconComponent" :size="20" />
				</template>
			</NcActionButton>
		</NcActions>
	</div>
</template>

<script>
import Vue from "vue";
import { mapStores } from "pinia";
import { showError } from "@nextcloud/dialogs";
import { debounce } from "debounce";
import Draggable from "vuedraggable";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import { api } from "../../api.js";
import FieldTypes from "../../models/FieldTypes.js";
import FieldsTable from "./FieldsTable.vue";
import FieldsTableRow from "./FieldsTableRow.vue";
import Field from "../Fields/Field.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		Draggable,
		NcActions,
		NcActionButton,
		FieldsTable,
		FieldsTableRow,
		Field,
	},
	data() {
		return {
			fields: [],
			addFieldMenuOpened: false,
			FieldTypes,
		};
	},
	computed: {
		...mapStores(useBiblioStore, useSettingsStore),
	},
	mounted() {
		api.getItemFields(this.settingsStore.context?.collectionId)
			.then((fields) => {
				this.fields = fields;
			})
			.catch(() => {
				showError(t("biblio", "Could not fetch item fields"));
			});
	},
	methods: {
		fieldsOrderChanged() {

		},
		onFieldsUpdate(fields) {
			console.log("onFieldsUpdate", JSON.stringify(fields));
			this.$emit("set-fields", fields);
		},
		onFieldUpdate(id, parameters) {
			api.updateItemField(this.settingsStore.context?.collectionId, id, parameters)
				.then((updatedField) => {
					const fieldIndex = this.fields.findIndex(field => field.id === id);
					Vue.set(this.fields, fieldIndex, updatedField);

					this.refreshItemFieldsInBiblioStoreIfNeeded();
				}).catch(() => {
					showError(t("biblio", "Could not update collection item field"));
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

					this.refreshItemFieldsInBiblioStoreIfNeeded();
				}).catch(() => {
					showError(t("biblio", "Could not create collection item field"));
				});
		},

		deleteItemField(itemFieldId) {
			return new Promise((resolve, reject) => {
				api.deleteItemField(this.settingsStore.context?.collectionId, itemFieldId)
					.then(() => {
						this.fields = this.fields.filter(itemField => itemField.id !== itemFieldId);

						this.refreshItemFieldsInBiblioStoreIfNeeded();

						resolve();
					})
					.catch(() => {
						showError(t("biblio", "Could not delete item field"));
						resolve();
					});
			});
		},

		refreshItemFieldsInBiblioStoreIfNeeded: debounce(() => {
			// the settings made changes to the item fields of the collection currently selected in the main application
			// refresh the data, so the changes take effect in the main application without a manual refresh

			if (this.biblioStore.selectedCollectionId === this.settingsStore.context?.collectionId) {
				this.biblioStore.fetchItemFields();
			}
		}, 2000),
	},
};
</script>

<style>
.addFieldButton {
	width: calc(100% - 24px);
	margin-left: 24px;
	margin-top: 5px;
	transition: opacity 0.5s;
}

.addFieldButton button {
	width: 100% !important;
}
</style>
