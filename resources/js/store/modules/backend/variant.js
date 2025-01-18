import axios from '../../../utils/axios.js';

export const state = {
    variants: [],
    variantValues: [],
    variant: null, // Store a single variant
};

export const mutations = {
    SET_VARIANTS(state, variants) {
        state.variants = variants;
    },
    SET_VARIANT(state, variant) {
        state.variant = variant; // Store a single variant
    },
    SET_VARIANT_VALUES(state, variantValues) {
        state.variantValues = variantValues;
    },
    ADD_VARIANT(state, variant) {
        state.variants.push(variant);
    },
    ADD_VARIANT_VALUE(state, variantValue) {
        state.variantValues.push(variantValue);
    },
    REMOVE_VARIANT(state, variantId) {
        state.variants = state.variants.filter(variant => variant.id !== variantId);
    },
    REMOVE_VARIANT_VALUE(state, { variantId, valueId }) {
        const variant = state.variants.find(v => v.id === variantId);
        if (variant) {
            variant.variant_values = variant.variant_values.filter(vv => vv.id !== valueId);
        }
    },
};

export const actions = {
    // Action to create a variant
    async createVariant({ commit }, variantData) {
        try {
            const response = await axios.post('/admin/variants', variantData);
            commit('ADD_VARIANT', response.data); // Add the created variant to the state
        } catch (error) {
            console.error('Error creating variant:', error);
        }
    },

    // Action to create a variant value
    async createVariantValue({ commit }, variantValueData) {
        try {
            const response = await axios.post('/admin/variant-values', variantValueData);
            commit('ADD_VARIANT_VALUE', response.data); // Add the created variant value to the state
        } catch (error) {
            console.error('Error creating variant value:', error);
        }
    },

    // Action to fetch variants
    async fetchVariants({ commit }) {
        try {
            const response = await axios.get('/admin/variants');
            commit('SET_VARIANTS', response.data);
        } catch (error) {
            console.error('Error fetching variants:', error);
        }
    },

    // Action to fetch variant values
    async fetchVariantValues({ commit }) {
        try {
            const response = await axios.get('/admin/variant-values');
            commit('SET_VARIANT_VALUES', response.data);
        } catch (error) {
            console.error('Error fetching variant values:', error);
        }
    },

    // Action to fetch a specific variant by ID
    async fetchVariantById({ commit }, id) {
        try {
            const response = await axios.get(`/admin/variants/${id}`);
            commit('SET_VARIANT', response.data); // Store the fetched variant
        } catch (error) {
            console.error('Error fetching variant by ID:', error);
        }
    },

    // Action to delete a variant
    async deleteVariant({ commit }, variantId) {
        try {
            await axios.delete(`/admin/variants/${variantId}`);
            commit('REMOVE_VARIANT', variantId); // Remove the variant from the state
        } catch (error) {
            console.error('Error deleting variant:', error);
        }
    },

    // Action to delete a variant value
    async deleteVariantValue({ commit }, { variantId, valueId }) {
        try {
            await axios.delete(`/admin/variant-values/${valueId}`);
            commit('REMOVE_VARIANT_VALUE', { variantId, valueId }); // Remove the variant value from the state
        } catch (error) {
            console.error('Error deleting variant value:', error);
        }
    },
};

export const getters = {
    getVariants: (state) => state.variants,
    getVariantValues: (state) => state.variantValues,
    getVariantById: (state) => state.variant, // Get the single variant by ID
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
