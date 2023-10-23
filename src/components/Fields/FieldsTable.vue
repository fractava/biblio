<template>
	<div>
		<table v-if="sortedFields.length > 0" class="fieldsTable">
			<colgroup>
				<col>
				<col v-if="enableIncludeInList">
				<col>
				<col span="2" style="width: 50%;">
			</colgroup>
			<Draggable :value="sortedFields"
				drag-class="drag"
				ghost-class="ghost"
				:animation="200"
				:force-fallback="true"
				tag="tbody"
				draggable=".draggableitem"
				handle=".drag-handle-active"
				@input="onFieldsUpdate"
				@start="isDragging = true"
				@end="isDragging = false">
				<Field v-for="field in sortedFields"
					:key="field.id"
					:name="field.name"
					:enable-include-in-list="enableIncludeInList"
					class="draggableitem"
					@update:name="(newName) => onFieldUpdate(field.id, { name: newName })"
					@update:includeInList="(newIncludeInList) => onFieldUpdate(field.id, { includeInList: newIncludeInList })"
					@delete="onFieldDelete(field.id)">
					<Icon :is="FieldTypes[field.type].iconComponent" slot="icon" v-tooltip="FieldTypes[field.type].label" />
					<component :is="FieldTypes[field.type].settingsComponent"
						slot="settings"
						class="fieldSettings"
						:settings="field.settings"
						@update:settings="(newSettings) => onFieldUpdate(field.id, {settings: newSettings})" />
				</Field>
			</Draggable>
		</table>
		<NcEmptyContent v-if="sortedFields.length === 0"
			:title="t('biblio', 'No fields')"
			style="margin-top: 20px; margin-bottom: 20px;">
			<template #icon>
				<GridOff />
			</template>
		</NcEmptyContent>
	</div>
</template>

<script>
import Draggable from "vuedraggable";
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js"

import GridOff from "vue-material-design-icons/GridOff.vue";

import Field from "./Field.vue";

import FieldTypes from "../../models/FieldTypes.js";

export default {
	components: {
		Draggable,
		NcEmptyContent,
		GridOff,
		Field,
	},
	props: {
		fields: {
			type: Array,
			default: () => [],
		},
		fieldsOrder: {
			type: Array,
			default: () => [],
		},
		enableIncludeInList: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			FieldTypes,
			isDragging: false,
		};
	},
	computed: {
		sortedFields() {
			return this.fields.toSorted((a, b) => this.fieldsOrder.indexOf(a.id) - this.fieldsOrder.indexOf(b.id));
		},
	},
	methods: {
		onFieldUpdate(fieldId, parameters) {
			this.$emit("field-update", fieldId, parameters);
		},
		onFieldsUpdate(fields) {
			let newFieldsOrder = [];

			for (const field of fields) {
				newFieldsOrder.push(field.id);
			}

			this.$emit("update:fieldsOrder", newFieldsOrder);
		},
		onFieldDelete(fieldId) {
			this.$emit("delete", fieldId);
		},
	},
};
</script>

<style>
.fieldsTable {
    width: 100%;
}

.fieldsTable tr:hover, .fieldsTable tr:focus, .fieldsTable tr:active {
    background-color: transparent;
}
</style>
