<template>
	<table class="fieldsTable">
		<colgroup>
			<col>
			<col>
			<col>
			<col span="2" style="width: 50%;">
		</colgroup>
		<Draggable :value="sortedFields"
			drag-class="drag"
			ghost-class="ghost"
			:animation="200"
			tag="tbody"
			draggable=".draggableitem"
			handle=".drag-handle-active"
			@input="onFieldsUpdate"
			@start="isDragging = true"
			@end="isDragging = false">
			<Field v-for="field in sortedFields"
				:key="field.id"
				:name="field.name"
				:include-in-list="!!field.includeInList"
				class="draggableitem"
				@update:name="(newName) => onFieldUpdate(field.id, { name: newName })"
				@update:includeInList="(newIncludeInList) => onFieldUpdate(field.id, { includeInList: newIncludeInList })"
				@delete="onFieldDelete(field.id)">
				<Icon :is="FieldTypes[field.type].iconComponent" slot="icon" v-tooltip="FieldTypes[field.type].label" />
				<component :is="FieldTypes[field.type].settingsComponent"
					slot="settings"
					:settings="field.settings"
					@update:settings="(newSettings) => onFieldUpdate(field.id, {settings: newSettings})" />
			</Field>
		</Draggable>
	</table>
</template>

<script>
import Draggable from "vuedraggable";

import Field from "./Field.vue";

import FieldTypes from "../../models/FieldTypes.js";

export default {
	components: {
		Draggable,
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
