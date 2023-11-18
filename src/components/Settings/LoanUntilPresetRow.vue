<template>
	<tr>
		<td>
			<NcTextField :value="preset.name"
				:error="preset.name?.length < 1"
				:label-visible="true"
				:helper-text="preset.name?.length > 1 ? '' : t('biblio', 'Invalid name')"
				:label="t('biblio', 'Name')"
				@update:value="updateName" />
		</td>
		<td>
			<vueSelect class="typeSelect"
				:options="options"
				:value="preset.type"
				:reduce="reduce"
				:placeholder="t('biblio', 'Select Type')"
				:clearable="false"
				@input="updateType" />
		</td>
		<td>
			<div v-if="preset.type === 'absolute'">
				<NcDateTimePickerNative :id="'loanUntilPresetInput' + preset.id"
					:value="new Date(preset.timestamp * 1000)"
					type="date"
					@input="updateTimestamp" />
			</div>
		</td>
	</tr>
</template>
<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";
import vueSelect from "vue-select";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcDateTimePickerNative from "@nextcloud/vue/dist/Components/NcDateTimePickerNative.js";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";

export default {
	components: {
		vueSelect,
		NcTextField,
		NcDateTimePickerNative,
	},
	props: {
		preset: {
			type: Object,
			default: () => ({}),
		},
	},
	data() {
		return {
			options: [
				{
					id: "relative",
					label: t("biblio", "Relative"),
				},
				{
					id: "absolute",
					label: t("biblio", "Absolute"),
				},
			],
		};
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	methods: {
		updateName: debounceFn(function(newName) {
			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				name: newName,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
		updateType: debounceFn(function(newType) {
			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				type: newType,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
		updateTimestamp: debounceFn(function(newTimestamp) {
			const unixTimestamp = Math.floor(new Date(newTimestamp).valueOf() / 1000);

			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				timestamp: unixTimestamp,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
		reduce(option) {
			return option.id;
		},
	},
};
</script>
