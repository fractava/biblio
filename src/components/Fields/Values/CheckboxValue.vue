<template>
	<div class="field__content">
		<div v-if="valueError">
			<FieldError @reset="resetValue" />
		</div>
		<div v-else>
			<NcCheckboxRadioSwitch :checked="validatedValue"
				:disabled="!allowValueEdit"
				@update:checked="onInput">{{ name }}</NcCheckboxRadioSwitch>
		</div>
	</div>
</template>

<script>
import NcCheckboxRadioSwitch from "@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js";

import FieldValue from "../../mixins/FieldValue.js";
import FieldError from "../FieldError.vue";

export default {
	components: {
		NcCheckboxRadioSwitch,
		FieldError,
	},
	mixins: [FieldValue],
	props: {
		value: {
			default: false,
		},
	},
	data() {
		return {
			defaultValue: false,
		};
	},
	computed: {
		valueError() {
			return !(typeof this.value === "boolean");
		},
		validatedValue() {
			if (typeof this.value === "boolean") {
				return this.value;
			} else {
				return this.defaultValue;
			}
		},
	},
	methods: {
		onInput(newValue) {
			this.$emit("update:value", newValue);
		},
		resetValue() {
			this.$emit("update:value", this.defaultValue);
		},
	},
};
</script>
