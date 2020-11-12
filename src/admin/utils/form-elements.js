import randomValueHex from "./helpers"
import countryList from "./countries"

export default  [
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Text input',
      type: 'text',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'TextInput',
      reportComponent: 'TextInputReport',
      hidden: false,
      hideInForm: false,
      class: '',
      placeholder: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      includeInAssessmentTitle: false,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'First and last name',
      type: 'first_last_name',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'TextInput',
      reportComponent: 'TextInputReport',
      hidden: false,
      hideInForm: false,
      class: '',
      placeholder: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      includeInAssessmentTitle: true,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Textarea input',
      type: 'textarea',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'TextareaInput',
      reportComponent: 'TextareaInputReport',
      hidden: false,
      hideInForm: false,
      class: '',
      placeholder: '',
      maxWidth: null,
      maxWidthUnit: 'px',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Select input',
      type: 'select',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'SelectInput',
      reportComponent: 'SelectInputReport',
      options: [],
      disableForScoring: false,
      hidden: false,
      hideInForm: false,
      class: '',
      placeholder: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      includeInAssessmentTitle: false,
      multiple: false,
      scoreGraphColor: "",
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Countries',
      type: 'select',
      defaultValue: null,
      label: 'Select country',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'CountryInput',
      reportComponent: 'CountryInputReport',
      options: countryList,
      hidden: false,
      hideInForm: false,
      class: '',
      placeholder: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      includeInAssessmentTitle: false,
      multiple: false,
      labelParts: [
        {
          name: 'Country name',
          key: 'name',
        }
        ],
      labelPartsSeperator: '-'
    },
  {
    key: randomValueHex(15),
    reportItemKey: "",
    name: 'Date input',
    type: 'date',
    defaultValue: null,
    label: '',
    reportLabel: '',
    conditions: {
      field: null,
      value: null,
    },
    required: false,
    canRemove: true,
    component: 'DateInput',
    reportComponent: 'DateInputReport',
    hidden: false,
    hideInForm: false,
    class: '',
    placeholder: '',
    maxWidth: null,
    maxWidthUnit: 'px',
  },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Radio input',
      type: 'radio',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'RadioInput',
      reportComponent: 'RadioInputReport',
      options: [],
      disableForScoring: false,
      hidden: false,
      hideInForm: false,
      class: '',
      includeInAssessmentTitle: false,
      maxWidth: null,
      maxWidthUnit: 'px',
      scoreGraphColor: null,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Radio grid input',
      type: 'radio_grid',
      defaultValue: null,
      label: '',
      reportLabel: '',
      conditions: [],
      required: false,
      canRemove: true,
      component: 'RadioGridInput',
      reportComponent: 'RadioGridInputReport',
      optionsHorizontal: [],
      optionsVertical: [],
      disableForScoring: false,
      hidden: false,
      hideInForm: false,
      class: '',
      selectedGraph: [{
        label: "Pie",
        key: "pie",
      }],
      maxWidth: null,
      maxWidthUnit: 'px',
      scoreGraphColor: null,
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'File upload',
      type: 'file_upload',
      label: '',
      reportLabel: '',
      conditions: [],
      component: 'FileUpload',
      reportComponent: 'FileUploadReport',
      canRemove: true,
      hidden: false,
      hideInForm: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      maxSize: null,
      maxFiles: 1,
      acceptedFileTypes: [],
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Page',
      type: 'page',
      conditions: [],
      component: 'Page',
      reportComponent: 'Page',
      canRemove: true,
      items: [],
      class: '',
      nextButtonText: '',
      previousButtonText: '',
      hidden: false,
      hideInForm: false,
      showTitle: false,
      maxWidth: null,
      maxWidthUnit: 'px',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Row',
      type: 'row',
      conditions: [],
      component: 'Row',
      reportComponent: 'Row',
      canRemove: true,
      items: [],
      hidden: false,
      hideInForm: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Items holder',
      type: 'column',
      conditions: [],
      component: 'Column',
      reportComponent: 'Column',
      canRemove: true,
      items: [],
      hidden: false,
      hideInForm: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Paragraph',
      type: 'paragraph',
      label: '',
      reportLabel: '',
      conditions: [],
      component: 'Paragraph',
      reportComponent: 'Paragraph',
      canRemove: true,
      hidden: false,
      hideInForm: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Part score',
      type: 'part_score',
      label: 'Part score',
      reportLabel: '',
      conditions: [],
      component: 'PartScore',
      reportComponent: 'PartScoreReport',
      canRemove: true,
      hidden: false,
      hideInForm: false,
      hideLabels: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      height: null,
      heightUnit: 'px',
      selectedResultType: [{
        label: "Score",
        key: "score",
      }],
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Total score',
      type: 'total_score',
      label: 'Total score',
      reportLabel: '',
      conditions: [],
      component: 'TotalScore',
      reportComponent: 'TotalScoreReport',
      canRemove: true,
      hidden: false,
      hideInForm: false,
      hideLabels: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      height: null,
      heightUnit: 'px',
      selectedResultType: [{
        label: "Score",
        key: "score",
      }],
    },
    {
      key: randomValueHex(15),
      reportItemKey: "",
      name: 'Legend',
      type: 'legend',
      label: '',
      reportLabel: '',
      conditions: [],
      component: 'Legend',
      reportComponent: 'LegendReport',
      canRemove: true,
      hidden: false,
      hideInForm: false,
      class: '',
      maxWidth: null,
      maxWidthUnit: 'px',
      legendFor: null,
    },
  ]



