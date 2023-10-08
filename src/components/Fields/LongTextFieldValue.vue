<template>
	<div class="field__content">
		<div v-if="allowValueEdit">
			<textarea ref="textarea"
				:placeholder="placeholder"
				:disabled="!allowValueEdit"
				:value="value"
				class="field__text"
				minlength="1"
				@input="onInput"
				@keypress="autoSizeText" />
		</div>
		<span v-else style="white-space: pre;">
			{{ value }}
		</span>
	</div>
</template>

<script>
import FieldValue from "../mixins/FieldValue.js";

export default {
	mixins: [FieldValue],
	props: {
		value: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			height: 1,
		};
	},
	watch: {
		allowValueEdit() {
			this.autoSizeText();
		},
	},
	mounted() {
		this.autoSizeText();
	},
	methods: {
		onInput() {
			const textarea = this.$refs.textarea;
			this.$emit("update:value", textarea.value);
			this.autoSizeText();
		},
		autoSizeText() {
			if (this.allowValueEdit) {
				const refresh = () => {
					const textarea = this.$refs.textarea;
					console.log(textarea);
					textarea.style.cssText = "height:auto; padding:0";
					textarea.style.cssText = `height: ${textarea.scrollHeight + 20}px`;
				};
				if (this.$refs.textarea) {
					refresh();
				} else {
					this.$nextTick(refresh);
				}
			}
		},
		onKeydownCtrlEnter(event) {
			this.$emit("keydown", event);
		},
	},
};
</script>

<style lang="scss" scoped>
.field__text {
	// make sure height calculations are correct
	box-sizing: content-box !important;
	width: 100%;
	min-width: 100%;
	max-width: 100%;
	min-height: 44px;
	margin: 0;
	padding: 6px 0;
	border: 0;
	border-bottom: 1px dotted var(--color-border-dark);
	border-radius: 0;
	resize: none;
	font-size: 14px;

	&:disabled {
		// Just overrides Server CSS-Styling for disabled inputs. -> Not Good??
		background-color: var(--color-main-background);
		color: var(--color-main-text);
	}
}

</style>
