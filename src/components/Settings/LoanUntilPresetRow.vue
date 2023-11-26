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
					:hide-label="true"
					type="date"
					@input="updateTimestampByDateString" />
			</div>
			<RelativeTimePicker v-else :timestamp="preset.timestamp" @update:timestamp="updateTimestamp" />
		</td>
		<td>
			<NcActions>
				<NcActionButton @click="onDelete">
					<template #icon>
						<Delete :size="20" />
					</template>
					{{ t('biblio', 'Delete Preset') }}
				</NcActionButton>
			</NcActions>
		</td>
	</tr>
</template>
<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";
import vueSelect from "vue-select";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcDateTimePickerNative from "@nextcloud/vue/dist/Components/NcDateTimePickerNative.js";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import Delete from "vue-material-design-icons/Delete.vue";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";
import RelativeTimePicker from "./RelativeTimePicker.vue";

export default {
	components: {
		vueSelect,
		NcTextField,
		NcDateTimePickerNative,
		Delete,
		RelativeTimePicker,
		NcActions,
		NcActionButton,
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
			if (newType === "absolute") {
				var newTimestamp = Math.floor(Date.now() / 1000);
			} else {
				var newTimestamp = 86400 * 365;
			}
			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				type: newType,
				timestamp: newTimestamp,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
		updateTimestampByDateString(newTimestamp) {
			const unixTimestamp = Math.floor(new Date(newTimestamp).valueOf() / 1000);
			this.updateTimestamp(unixTimestamp);
		},
		updateTimestamp: debounceFn(function(newTimestamp) {
			// optimistic update
			this.$emit("update:preset", Object.assign(this.preset, {
				timestamp: newTimestamp,
			}));
			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				timestamp: newTimestamp,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
		async onDelete() {
			await api.deleteLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id);
			this.$emit("refresh");
		},
		reduce(option) {
			return option.id;
		},
	},
};
</script>
<style scoped>
.typeSelect {
	min-width: 100px;
}
</style>
