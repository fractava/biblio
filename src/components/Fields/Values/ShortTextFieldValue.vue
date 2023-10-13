<template>
	<div class="field__content">
		<div v-if="valueError">
			<FieldError @reset="resetValue" />
		</div>
		<div v-else>
			<NcInputField v-if="allowValueEdit"
				:placeholder="placeholder"
				:disabled="!allowValueEdit"
				:value="validatedValue"
				minlength="0"
				type="text"
				@update:value="onInput"
				@keydown.enter.exact.prevent="onKeydownEnter" />
			<p v-else>
				{{ validatedValue }}
			</p>
		</div>
	</div>
</template>

<script>
import NcInputField from "@nextcloud/vue/dist/Components/NcInputField.js";

import FieldValue from "../../mixins/FieldValue.js";
import FieldError from "../FieldError.vue";

export default {
	components: {
		NcInputField,
		FieldError,
	},
	mixins: [FieldValue],
	props: {
		value: {
			default: "",
		},
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
