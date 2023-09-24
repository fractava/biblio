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
				<Fields :is="FieldTypes[field.type].component"
					v-for="field in fields"
					:key="field.title + '-field'"
					:field-type="FieldTypes[field.type]"
					:is-required="false"
					:allow-title-edit="true"
					:allow-value-edit="true"
					:enable-drag-handle="true"
					:title.sync="field.name"
					:settings="field.settings"
					class="draggableitem"
					@update:value="(newValue) => onFieldUpdate(newValue, field)"
					@delete="deleteField(field)" />
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
			</NcActionButton>
		</NcActions>
	</div>
</template>

<script>
import { mapStores } from "pinia";
import Draggable from "vuedraggable";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import FieldTypes from "../../models/FieldTypes.js";
import FieldsTable from "./FieldsTable.vue";
import FieldsTableRow from "./FieldsTableRow.vue";

import { useBiblioStore } from "../../store/biblio.js";
import { useSettingsStore } from "../../store/settings.js";

export default {
	components: {
		Draggable,
		NcActions,
		NcActionButton,
		FieldsTable,
		FieldsTableRow,
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
	async mounted() {
		this.fields = await this.biblioStore.getCollectionItemFields(this.settingsStore.context?.collectionId);
	},
	methods: {
		fieldsOrderChanged() {

		},
		onFieldsUpdate(fields) {
			console.log("onFieldsUpdate", JSON.stringify(fields));
			this.$emit("set-fields", fields);
		},
		onFieldUpdate() {
		},
		addField(type, field) {
			this.biblioStore.createCollectionItemField(this.settingsStore.context?.collectionId, {
				type,
				name: "",
				settings: field.defaultSettings,
				includeInList: false,
			});
		},
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
