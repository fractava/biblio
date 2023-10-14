<template>
	<div class="field__content">
		<div v-if="valueError">
			<FieldError @reset="resetValue" />
		</div>
		<div v-else>
			<div v-if="allowValueEdit">
				<NcSelect class="limitSelect"
					:options="settings?.options"
					:value="selected"
					@input="value => $emit('update:value', value.id)" />
			</div>
			<div v-else>
				{{ selected?.label }}
			</div>
		</div>
	</div>
</template>

<script>
import NcSelect from "@nextcloud/vue/dist/Components/NcSelect.js";

import FieldValue from "../../mixins/FieldValue.js";
import FieldError from "../FieldError.vue";

export default {
	components: {
		NcSelect,
		FieldError,
	},
	mixins: [FieldValue],
	props: {
		value: {
			default: "",
		},
		settings: {
			type: Object,
			default: () => ({ options: [] }),
		},
	},
	data() {
		return {
			defaultValue: "",
			defaultSettings: { options: [] },
		};
	},
	computed: {
		valueError() {
			return !(typeof this.value === "string" || this.value instanceof String);
		},
		validatedValue() {
			if (typeof this.value === "string" || this.value instanceof String) {
				return this.value;
			} else {
				return this.defaultValue;
			}
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
		selected() {
			return this.validatedSettings?.options?.find((option) => (option.id === this.validatedValue));
		},
	},
	methods: {
		resetValue() {
			this.$emit("update:value", this.defaultValue);
		},
	},
};

</script>
