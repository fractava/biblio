<template>
	<div>
		<NcTextField class="searchTextField"
			:value.sync="localValue"
			:label="t('biblio', 'Search')"
			trailing-button-icon="close"
			:show-trailing-button="localValue !== ''"
			@trailing-button-click="clearValue"
			@update:value="submit">
			<Magnify :size="25" />
		</NcTextField>
	</div>
</template>

<script>
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import Magnify from "vue-material-design-icons/Magnify.vue";

export default {

	components: {
		Magnify,
		NcTextField,
	},

	props: {
		searchString: {
			type: String,
			default: null,
		},
	},

	data() {
		return {
			localValue: "",
		};
	},

	methods: {
		clearValue() {
			this.localValue = "";
			this.submit();
		},
		submit() {
			this.$emit("set-search-string", this.localValue);
		},
	},

};
</script>
<style lang="scss">
.searchTextField {
	&.input-field, .input-field__main-wrapper {
		height: 48px;

		input {
			padding: 22px 35px;
			font-size: 15px;
		}

		.input-field__icon {
		    bottom: 8px;
		}
	}
}

</style>
