<template>
	<tr v-if="row" :class="{ selected }">
		<td class="sticky">
			<NcCheckboxRadioSwitch :checked="selected" @update:checked="v => $emit('update-row-selection', { rowId: row.id, value: v })" />
		</td>
		<td v-for="col in columns"
			:key="col.id"
			:class="{ clickable: col.clickable }"
			@click="col.clickable && ($emit('click-row', row.id, col.id))">
			<component :is="col.cellComponent"
				v-if="col.isProperty || row?.fieldValues"
				:column="col"
				:row-id="row.id"
				:value="getCellValue(col)"
				:settings="getCellSettings(col)" />
		</td>
	</tr>
</template>

<script>
import NcCheckboxRadioSwitch from "@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import Pencil from "vue-material-design-icons/Pencil.vue";

export default {
	name: "TableRow",
	components: {
		NcButton,
		Pencil,
		NcCheckboxRadioSwitch,
	},

	props: {
		row: {
			type: Object,
			default: () => {},
		},
		columns: {
			type: Array,
			default: () => [],
		},
		selected: {
			type: Boolean,
			default: false,
		},
		config: {
			type: Object,
			default: null,
		},
	},
	computed: {
		getSelection: {
			get: () => { return this.selected; },
			set: () => { alert("updating selection"); },
		},
	},
	methods: {
		getCellValue(column) {
			if (!this.row) {
				return null;
			}

			if (column.isProperty) {
				if (Array.isArray(column.property)) {
					let subProperty = this.row;
					for (const subPropertyPath of column.property) {
						subProperty = subProperty?.[subPropertyPath];
					}
					return subProperty;
				} else {
					return this.row?.[column.property];
				}
			} else {
				const field = this?.row?.fieldValues?.find(item => item.fieldId === column.id) || null;

				if (field) {
					return field.value;
				} else {
					// if no value is given, try to get the default value from the field definition
					return column.defaultValue;
				}
			}
		},
		getCellSettings(column) {
			if (!this.row) {
				return null;
			}

			if (column.isProperty) {
				return {};
			} else {
				const field = this?.row?.fieldValues?.find(item => item.fieldId === column.id) || null;

				if (field) {
					return field.settings;
				} else {
					return column.defaultSettings;
				}
			}
		},
		truncate(text) {
			if (text.length >= 400) {
				return text.substring(0, 400) + "...";
			} else {
				return text;
			}
		},
	},
};
</script>

<style scoped lang="scss">

tr.selected {
	background-color: var(--color-primary-light) !important;
}

:deep(.checkbox-radio-switch__icon) {
	margin: 0;
}

td.clickable, td.clickable * {
	cursor: pointer;
}

</style>
