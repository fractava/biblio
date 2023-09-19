<template>
	<div>
		<div class="editModeContainer">
			<NcButton aria-label="Start/Stop editing mode"
				@click="editMode = !editMode">
				<template #icon>
					<Pencil :size="20" />
				</template>
				<template>Edit</template>
			</NcButton>
		</div>
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
				<ShortTextField slot="header"
					:enable-drag-handle="false"
					:field-type="FieldTypes['short']"
					:allow-title-edit="false"
					:is-required="true"
					title="Titel"
					:value="title" />
				<Fields :is="FieldTypes[field.type].component"
					v-for="field in fields"
					:key="field.title + '-field'"
					:field-type="FieldTypes[field.type]"
					:is-required="false"
					:allow-title-edit="editMode"
					:allow-value-edit="editMode"
					:enable-drag-handle="editMode"
					:title.sync="field.title"
					:value="field.value"
					class="draggableitem"
					@update:value="(newValue) => onFieldUpdate(newValue, field)"
					@delete="deleteField(field)" />
			</Draggable>
		</FieldsTable>
	</div>
</template>

<script>
import Draggable from "vuedraggable";

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import Pencil from 'vue-material-design-icons/Pencil.vue';
import FieldTypes from "../models/FieldTypes";
import ShortTextField from "../components/Fields/ShortTextField";

import FieldsTable from "../components/FieldsTable.vue";
import FieldsTableRow from "../components/FieldsTableRow.vue";

export default {
	components: {
		Draggable,
		NcButton,
		Pencil,
		FieldsTable,
		FieldsTableRow,
		ShortTextField,
	},
	props: {
		title: {
			type: String,
			default: "",
		},
		fields: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
			editMode: false,
			FieldTypes,
		};
	},
	methods: {
		fieldsOrderChanged() {

		},
		onFieldsUpdate(fields) {
			// remove falsy entries, draggable has a bug that sometimes inserts undefined entries
			fields = fields.filter(Boolean);
			this.$emit("setFields", fields);
		},
		onFieldUpdate() {
		},
	},
};
</script>

<style scoped>

.editModeContainer {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 20px;
}

.ignoreForLayout {
	display: contents;
}
</style>
