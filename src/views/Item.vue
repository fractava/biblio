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
		<FieldsValueTable :field-values="item.fieldValues"
			:edit-mode="editMode"
			@update:value="updateValue">
			<template #head>
				<!--<ShortTextFieldValue slot="header"
					:enable-drag-handle="false"
					:field-type="FieldTypes['short']"
					:allow-title-edit="false"
					:is-required="true"
					name="Titel"
					:value="item.title" />-->
			</template>
		</FieldsValueTable>
		<h2>Instances:</h2>
		<AddItemInstance :item-id="itemId" />
		<table class="itemInstancesTable">
			<tbody>
				<tr v-for="instance in instances" :key="instance.id">
					<td>{{ instance?.barcode }}</td>
					<td>{{ instance?.loanedTo }}</td>
					<td>{{ instance?.loanedUntil }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import { mapStores } from "pinia";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";

import { api } from "../api";
import Pencil from "vue-material-design-icons/Pencil.vue";
import ShortTextFieldValue from "../components/Fields/ShortTextFieldValue.vue";
import FieldsValueTable from "../components/FieldsValueTable.vue";

import { useBiblioStore } from "../store/biblio.js";
import AddItemInstance from "../components/AddItemInstance.vue";

export default {
	components: {
		NcButton,
		Pencil,
		ShortTextFieldValue,
		FieldsValueTable,
		AddItemInstance,
	},
	props: {
	},
	data() {
		return {
			editMode: false,
			instances: [],
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		itemId() {
			return parseInt(this.$route.params.id);
		},
		item() {
			return this.biblioStore.getItemById(this.itemId);
		},
	},
	async mounted() {
		this.instances = await api.getItemInstances(this.biblioStore.selectedCollectionId, this.itemId);
	},
	methods: {
		updateValue(newValue, field) {
			api.updateItemFieldValue(this.biblioStore.selectedCollectionId, this.itemId, field.fieldId, {
				value: newValue,
			});
		},
	},
};
</script>

<style lang="scss" scoped>

.editModeContainer {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 20px;
}

.ignoreForLayout {
	display: contents;
}

.itemInstancesTable {
	width: 100%;
	border-collapse: collapse;

	tr, td, th {
		border: 1px black solid;
	}

	td, th {
		padding: 5px;
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
</style>
