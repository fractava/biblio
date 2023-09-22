<template>
	<div class="navigationSelectContainer">
		<vueSelect class="navigationSelectSelect"
			:options="options"
			:label="optionsLabel"
			:placeholder="placeholder">
			<template #open-indicator="{ attributes }">
				<OpenIndicator v-bind="attributes" />
				<div @mousedown.stop>
					<NcActions ref="actions"
						:inline="1"
						class="navigationSelectButton"
						type="secondary"
						container="#app-navigation-vue">
						<NcActionButton :aria-label="buttonAriaLabel"
							@click.stop="handleButton">
							<template #icon>
								<slot name="button-icon" />
							</template>
						</NcActionButton>
					</NcActions>
				</div>
			</template>
		</vueSelect>
	</div>
</template>

<script>
import vueSelect from 'vue-select';
import OpenIndicator from 'vue-select/src/components/OpenIndicator.vue';

import 'vue-select/dist/vue-select.css';
import NcActions from '@nextcloud/vue/dist/Components/NcActions.js';
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js';

export default {
	components: {
		vueSelect,
		OpenIndicator,
		NcActions,
		NcActionButton,
	},
	props: {
		/**
		 * Displays a loading animated icon on the left of the element
		 * instead of the icon.
		 */
		loading: {
			type: Boolean,
			default: false,
		},

		buttonAriaLabel: {
			type: String,
			required: true,
		},

		options: {
			type: Array,
			default: [],
		},

		optionsLabel: {
			type: String,
			default: "label",
		},

		placeholder: {
			type: String,
			default: "",
		},
	},
	methods: {
		handleButton(event) {
		},
	},
};
</script>

<style scoped>
.navigationSelectContainer {
    display: flex;
    flex-direction: row;
    width: 100%;
    padding: 8px;
    --vs-search-input-bg: var(--color-primary-element-light);
    --vs-dropdown-bg: var(--color-primary-element-light);
    --vs-dropdown-option--active-bg: var(--color-primary-element-light-hover);
    --vs-controls-color: var(--color-primary-element-light-text);
    --vs-search-input-color: var(--color-primary-element-light-text);
    --vs-border-color: transparent;
    --vs-border-width: 0px;
    /*--vs-dropdown-option-padding: 4px 4px;*/
}

.navigationSelectSelect {
    margin: 0px;
    width: 100%;
}

.navigationSelectContainerIcon {
    display: flex;
    align-items: center;
    flex: 0 0 44px;
    justify-content: center;
    width: 44px;
    height: 44px;
}
</style>

<style lang="scss">
.navigationSelectSelect {
    .vs__selected-options {
        height: 44px;
    }

    .vs__selected {
        padding: 0 0.5em;
        margin-top: 0px;
        height: 100%;
    }

    .vs__search, .vs__search:focus {
        margin-top: 0px;
        height: 100% !important;
        border: none;
    }

    .vs__dropdown-toggle {
        border-radius: 24px;
        padding-bottom: 0px;
        overflow: hidden;
    }

    .vs__actions {
        padding-right: 0px;
        padding-top: 0px;
    }

    .navigationSelectButton {
        margin-left: 5px;
        border-top-left-radius: 0px !important;
        border-bottom-left-radius: 0px !important;
    }

    .vs__dropdown-toggle:hover:not(:has(.navigationSelectButton:hover)) {
        background-color: var(--color-primary-element-light-hover);
    }

    &.vs--open {
        .navigationSelectButton {
            border-bottom-right-radius: 0px !important;
        }
    }

    .vs__dropdown-menu {
        border-color: var(--vs-border-color) !important;
    }
}
</style>
