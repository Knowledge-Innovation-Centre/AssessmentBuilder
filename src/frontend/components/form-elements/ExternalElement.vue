<template>
  <div>
    <component
      :is="getComponent"
      :has-error="hasError"
      :object="object.element"
    />
  </div>
</template>

<script>
import itemsHelper from "../../mixins/itemsHelpers";
import SelectInput from "./SelectInput.vue";
import LOCSelectInput from "./LOCSelectInput.vue";
import CountryInput from "./CountryInput.vue";
import TextareaInput from "./TextareaInput.vue";
import TextInput from "./TextInput.vue";
import HiddenInput from "./HiddenInput.vue";
import RadioInput from "./RadioInput.vue";
import DateInput from "./DateInput.vue";
import RadioGridInput from "./RadioGridInput.vue";

export default {
  name: "ExternalElement",

  components: {
    SelectInput,
    LOCSelectInput,
    CountryInput,
    TextareaInput,
    TextInput,
    HiddenInput,
    RadioInput,
    DateInput,
    RadioGridInput
  },

  mixins: [itemsHelper],

  props: {
    object: {
      type: Object,
      required: true
    },
    hasError: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data() {
    return {
      options: []
    };
  },

  computed: {
    getComponent() {
      if (!this.object.element) {
        return "";
      }
      return this.object.element.component;
    }
  },
  mounted() {},
  methods: {}
};
</script>
