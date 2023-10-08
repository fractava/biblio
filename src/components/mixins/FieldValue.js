export default {
	props: {
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
