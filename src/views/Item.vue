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
		<!--<ShortTextFieldValue slot="header"
			:enable-drag-handle="false"
			:field-type="FieldTypes['short']"
			:allow-title-edit="false"
			:is-required="true"
			name="Titel"
			:value="item.title" />-->
		<table>
			<tbody>
				<tr v-for="field in item.fieldValues" :key="field.id">
					<td>{{ field.name }}</td>
					<td>
						<FieldValue :is="FieldTypes[field.type].valueComponent"
							:allow-value-edit="true"
							:value="field.value"
							@update:value="(newValue) => {updateValue(newValue, field)}" />
					</td>
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

import { useBiblioStore } from "../store/biblio.js";
import FieldTypes from "../models/FieldTypes";

export default {
	components: {
		NcButton,
		Pencil,
		ShortTextFieldValue,
	},
	props: {
	},
	data() {
		return {
			editMode: false,
			FieldTypes,
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		itemId() {
			return this.$route.params.id;
		},
		item() {
			return this.biblioStore.getItemById(this.itemId);
		},
	},
	methods: {
		updateValue(newValue, field) {
			api.updateItemFieldValue(this.biblioStore.selectedCollectionId, this.itemId, field.id, {
				value: newValue,
			});
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
