<template>
	<table class="fieldsValueTable">
		<colgroup>
			<col span="2" style="width: 50%;">
		</colgroup>
		<tbody>
			<tr>
				<th>{{ t('biblio', 'Name') }}</th>
				<th>{{ t('biblio', 'Value') }}</th>
			</tr>
			<slot name="head" />
			<tr v-for="field in fieldValues" :key="field.fieldId">
				<td>{{ field.name }}</td>
				<td>
					<FieldValue :is="FieldTypes[field.type].valueEditComponent"
						:field-type="FieldTypes[field.type]"
						:allow-value-edit="editMode"
						:value="field.value"
						:settings="field.settings"
						@update:value="(newValue) => { updateValue(newValue, field) }" />
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
import FieldTypes from "../models/FieldTypes";

export default {
	props: {
		fieldValues: {
			type: Array,
			default: () => [],
		},
		editMode: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			FieldTypes,
		};
	},
	methods: {
		updateValue(newValue, field) {
			// optimistic update
			field.value = newValue;

			this.$emit("update:value", newValue, field);
		},
	},
};
</script>
<style lang="scss" scoped>
.fieldsValueTable {
	width: 100%;
	border-collapse: collapse;
	/*table-layout: fixed;*/

	th {
		font-weight: bold;
	}

	td, th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
	}

	tr:hover, tr:focus, tr:active {
		background-color: var(--color-background-dark);
	}
}
</style>
