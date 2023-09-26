export default {
	props: {
		value: {
			type: String,
			default: "",
		},
		allowValueEdit: {
			type: Boolean,
			deafult: false,
		},
		placeholder: {
			type: String,
			default: t("biblio", "Value"),
		},
		fieldType: {
			type: Object,
			default: {},
		},
	},
};
