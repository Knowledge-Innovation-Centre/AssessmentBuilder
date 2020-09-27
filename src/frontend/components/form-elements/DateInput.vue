<template>
  <div>
        <label for="label">
          {{ object.label }} <template v-if="object.required">*</template>
          <date-picker v-model="localDate"  :placeholder="object.placeholder"></date-picker>
        </label>
    </div>
</template>

<script>

import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

  export default {

    name: 'TextInput',

    components: {
      DatePicker
    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    data () {
      return {
        localDate: null
      };
    },

    computed: {

    },

    watch: {
      localDate() {
        this.$store.dispatch('updateValue', { key: this.object.key, value: this.localDate})
      }
    },


    methods: {
    }
  };
</script>
