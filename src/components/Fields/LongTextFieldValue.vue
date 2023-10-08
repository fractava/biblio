<template>
	<div class="field__content">
		<div v-if="allowValueEdit">
			<NcRichContenteditable :auto-complete="() => {}"
				:value="value"
				:multiline="true"
				@update:value="onInput" />
		</div>
		<div v-else>
			<NcRichText :text="value"
				:autolink="true"
				:use-markdown="true" />
		</div>
	</div>
</template>

<script>
import debounceFn from "debounce-fn";

import NcRichContenteditable from "@nextcloud/vue/dist/Components/NcRichContenteditable.js";
import NcRichText from "@nextcloud/vue/dist/Components/NcRichText.js";

import FieldValue from "../mixins/FieldValue.js";

export default {
	components: {
		NcRichContenteditable,
		NcRichText,
	},
	mixins: [FieldValue],
	props: {
		value: {
			type: String,
			default: "",
		},
	},
	methods: {
		onInput: debounceFn(function(newValue) {
			this.$emit("update:value", newValue);
		}, { wait: 500 }),
	},
};
</script>
<style scoped>
.field__content {
	width: 100%;
    white-space: initial;
}
</style>
