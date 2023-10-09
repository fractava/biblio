<template>
	<div>
		<h1 class="sectionHeader">
			{{ t("biblio", "Item Properties") }}
		</h1>
		<div class="editModeContainer">
			<NcButton aria-label="Start/Stop editing mode"
				@click="editMode = !editMode">
				<template #icon>
					<Pencil :size="20" />
				</template>
				Edit
			</NcButton>
		</div>
		<FieldsValueTable :field-values="item.fieldValues"
			:edit-mode="editMode"
			@update:value="updateValue">
			<template #head>
				<tr>
					<td>{{ t("biblio", "Title") }}</td>
					<td>
						<ShortTextFieldValue :field-type="FieldTypes['short']"
							:value="item.title"
							:allow-value-edit="true" />
					</td>
				</tr>
			</template>
		</FieldsValueTable>
		<h1 class="sectionHeader">
			{{ t("biblio", "Item Instances") }}
		</h1>
		<AddItemInstance :item-id="itemId" @added-instance="onAddedInstance" />
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
import Vue from "vue";
import { mapStores } from "pinia";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";

import { api } from "../api";
import Pencil from "vue-material-design-icons/Pencil.vue";
import ShortTextFieldValue from "../components/Fields/ShortTextFieldValue.vue";
import FieldsValueTable from "../components/FieldsValueTable.vue";

import { useBiblioStore } from "../store/biblio.js";
import AddItemInstance from "../components/AddItemInstance.vue";

import FieldTypes from "../models/FieldTypes.js";

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
			FieldTypes,
			editMode: false,
			instances: [],
			item: {},
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		itemId() {
			return parseInt(this.$route.params.id);
		},
	},
	mounted() {
		api.getItem(this.biblioStore.selectedCollectionId, this.itemId).then((result) => {
			this.item = result;
		});
		api.getItemInstances(this.biblioStore.selectedCollectionId, this.itemId).then((result) => {
			this.instances = result;
		});
	},
	methods: {
		updateValue(newValue, field) {
			api.updateItemFieldValue(this.biblioStore.selectedCollectionId, this.itemId, field.fieldId, {
				value: newValue,
			}).then((result) => {
				const updatedIndex = this.item.fieldValues.findIndex(fieldValue => fieldValue.fieldId === field.fieldId);
				console.log(result, updatedIndex);
				Vue.set(this.item.fieldValues, updatedIndex, result);
			});
		},
		onAddedInstance(newInstance) {
			this.instances.push(newInstance);
		},
	},
};
</script>

<style lang="scss" scoped>

.sectionHeader {
	text-align:center;
	font-size: 2em;
	margin-top: 0.67em;
	margin-bottom: 0.67em;
	font-weight: bold;
}

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
