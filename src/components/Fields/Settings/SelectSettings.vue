<template>
	<div>
		<div v-if="settingsError">
			<FieldError @reset="resetSettings" />
		</div>
		<div v-else class="container">
			<div v-for="option in validatedSettings.options"
				:key="option.id"
				style="display: flex; align-items: center;">
				<NcTextField class="entry"
					:value="option.label"
					:show-trailing-button="false"
					:label="t('biblio', 'Select Option')" />
				<NcActions>
					<NcActionButton @click="deleteEntry(option.id)">
						<template #icon>
							<Delete :size="20" />
						</template>
						{{ t('biblio', 'Delete Option') }}
					</NcActionButton>
				</NcActions>
			</div>
			<NcTextField class="addNewOption"
				:value.sync="newOptionValue"
				:show-trailing-button="!isNewOptionEmpty"
				:label="t('biblio', 'Add Option')"
				trailing-button-icon="arrowRight"
				@trailing-button-click="addNewOption"
				@keydown.enter.prevent="addNewOption">
				<Plus :size="20" />
			</NcTextField>
		</div>
	</div>
</template>

<script>
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import GenRandomId from "../../../utils/GenRandomId.js";

import FieldError from "../FieldError.vue";

import Plus from "vue-material-design-icons/Plus.vue";
import Delete from "vue-material-design-icons/Delete.vue";

export default {
	components: {
		NcTextField,
		NcActions,
		NcActionButton,
		FieldError,
		Plus,
		Delete,
	},
	props: {
		settings: {
			default: () => ({ options: [] }),
		},
	},
	data() {
		return {
			defaultSettings: { options: [] },
			newOptionValue: "",
		};
	},
	computed: {
		isNewOptionEmpty() {
			return this.newOptionValue?.trim().length === 0;
		},
		hasNoOption() {
			return !this.settings?.option?.length;
		},
		settingsError() {
			return !(typeof this.settings === "object" && !Array.isArray(this.settings) && this.settings !== null);
		},
		validatedSettings() {
			if (typeof this.settings === "object" && !Array.isArray(this.settings) && this.settings !== null) {
				return this.settings;
			} else {
				return this.defaultSettings;
			}
		},
	},
	methods: {
		addNewOption() {
			if (!this.isNewOptionEmpty) {
				const options = this.settings.options || [];
				this.$emit("update:settings", {
					options: [
						...options,
						{
							id: GenRandomId(),
							label: this.newOptionValue,
						},
					],
				});

				this.newOptionValue = "";
			}
		},
		deleteEntry(id) {
			const options = this.settings.options || [];
			this.$emit("update:settings", {
				options: options.filter((option) => (option.id !== id)),
			});
		},
		resetSettings() {
			this.$emit("update:settings", this.defaultSettings);
		},
	},
};
</script>
