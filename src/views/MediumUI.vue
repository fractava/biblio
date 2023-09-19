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
				<ShortTextField slot="header"
					:enable-drag-handle="false"
					:field-type="FieldTypes['short']"
					:allow-title-edit="false"
					:is-required="true"
					title="Titel"
					:value="title" />
			</Draggable>
		</FieldsTable>
		<NcActions class="addFieldButton"
			:style="{'opacity': editMode ? 1 : 0}"
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
import Draggable from "vuedraggable";

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js';
import NcActions from '@nextcloud/vue/dist/Components/NcActions.js';
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js';

import Pencil from 'vue-material-design-icons/Pencil.vue';
import FieldTypes from "../models/FieldTypes";
import ShortTextField from "../components/Fields/ShortTextField";

import FieldsTable from "../components/FieldsTable.vue";
import FieldsTableRow from "../components/FieldsTableRow.vue";

export default {
	components: {
		Draggable,
		NcButton,
		NcActions,
		NcActionButton,
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
			addFieldMenuOpened: false,
			FieldTypes,
		};
	},
	methods: {
		fieldsOrderChanged() {

		},
		onFieldsUpdate(fields) {
			console.log("onFieldsUpdate", JSON.stringify(fields));
			this.$emit("setFields", fields);
		},
		onFieldUpdate() {
		},
		addField(type, field) {
			this.onFieldsUpdate([...this.fields, {
				title: field.label,
				type,
				value: field.defaultValue,
			}]);
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
