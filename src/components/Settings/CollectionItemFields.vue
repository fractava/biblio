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
					:key="field.id + '-field'"
					v-bind.sync="$attrs"
					:name="field.name"
					:edit="true"
					:allow-name-edit="true"
					:allow-deletion="true"
					:enable-drag-handle="true"
					:name-placeholder="t('biblio', 'Name')"
					class="draggableitem"
					@update:name="(newName) => onFieldUpdate(field.id, {name: newName})"
					@delete="deleteField(field)">
					<FieldSettings :is="FieldTypes[field.type].settingsComponent" />
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
		onFieldUpdate(id, options) {
			this.biblioStore.updateCollectionItemField(this.settingsStore.context?.collectionId, id, options);
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
