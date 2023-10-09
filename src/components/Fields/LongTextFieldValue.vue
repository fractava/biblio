<template>
	<div class="field__content">
		<div v-if="valueError">
			<FieldError @reset="resetValue" />
		</div>
		<div v-else>
			<div v-if="allowValueEdit">
				<NcRichContenteditable :auto-complete="() => {}"
					:value="validatedValue"
					:multiline="true"
					@update:value="onInput" />
			</div>
			<div v-else>
				<NcRichText :text="validatedValue"
					:autolink="true"
					:use-markdown="true" />
			</div>
		</div>
	</div>
</template>

<script>
import debounceFn from "debounce-fn";

import NcRichContenteditable from "@nextcloud/vue/dist/Components/NcRichContenteditable.js";
import NcRichText from "@nextcloud/vue/dist/Components/NcRichText.js";

import FieldValue from "../mixins/FieldValue.js";
import FieldError from "./FieldError.vue";

export default {
	components: {
		NcRichContenteditable,
		NcRichText,
		FieldError,
	},
	mixins: [FieldValue],
	props: {
		value: {
			default: "",
		},
	},
	data() {
		return {
			defaultValue: "",
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
	},
	methods: {
		onInput: debounceFn(function(newValue) {
			this.$emit("update:value", newValue);
		}, { wait: 500 }),
		resetValue() {
			this.$emit("update:value", this.defaultValue);
		},
	},
};
</script>
<style scoped>
.field__content {
	width: 100%;
    white-space: initial;
}
</style>
