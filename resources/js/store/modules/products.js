import axios from "axios";
import { api } from "~/config";
import * as types from "../mutation-types";

/**
 * Initial state
 */
export const state = {
  data: []
};

/**
 * Mutations
 */
export const mutations = {
  [types.SET_PRODUCTS_DATA](state, payload) {
    state.data = payload;
  }
};

/**
 * Actions
 */
export const actions = {
  async fetch({ commit }) {
    const { data } = await axios.get(api.path("products"));
    commit(types.SET_PRODUCTS_DATA, data);
  },
  async save({ dispatch }, payload) {
    await axios.post(api.path("products"), {
      data: payload
    });
    dispatch("fetch");
  },
  async delete({ dispatch }, payload) {
    await axios.delete(`${api.path("products")}/${payload}`);
    dispatch("fetch");
  },
  async update({ dispatch }, payload) {
    await axios.patch(`${api.path("products")}/${payload.id}`, {
      data: payload
    });
    dispatch("fetch");
  }
};

/**
 * Getters
 */
export const getters = {
  data: state => state.data
};
