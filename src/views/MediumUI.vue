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
			<FieldsTableRow>
				<div></div>
				<template #left>
					<span>Titel:</span>
				</template>
				<template #right>
					<span>{{ title }}</span>
				</template>
			</FieldsTableRow>
			<Draggable :value="fields"
				@input="onFieldsUpdate"
				:animation="200"
				tag="div"
				class="ignoreForLayout"
				handle=".drag-handle"
				@start="isDragging = true"
				@end="isDragging = false"
				@change="fieldsOrderChanged">
				<Fields :is="FieldTypes[field.type].component"
					v-for="field in fields"
					:key="field.title + '-field'"
					ref="fields"
					:field-type="FieldTypes[field.type]"
					:is-required="false"
					:title.sync="field.title"
					:value="field.value"
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

import FieldsTable from "../components/FieldsTable.vue";
import FieldsTableRow from "../components/FieldsTableRow.vue";

export default {
	components: {
		Draggable,
		NcButton,
		Pencil,
		FieldsTable,
		FieldsTableRow,
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
