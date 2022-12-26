import React from 'react';
import { registerBlockType } from '@wordpress/blocks';
import SprocketIcon from '../Common/SprocketIcon';
import FormBlockSave from './FormBlockSave';
import { connectionStatus } from '../../constants/leadinConfig';
import FormGutenbergPreview from './FormGutenbergPreview';
import ErrorHandler from '../../shared/Common/ErrorHandler';
import FormEdit from '../../shared/Form/FormEdit';
import { __ } from '@wordpress/i18n';

const ConnectionStatus = {
  Connected: 'Connected',
  NotConnected: 'NotConnected',
};

export default function registerFormBlock() {
  const editComponent = (props: any) => {
    if (props.attributes.preview) {
      return <FormGutenbergPreview />;
    } else if (connectionStatus === ConnectionStatus.Connected) {
      return <FormEdit {...props} />;
    } else {
      return <ErrorHandler status={401} />;
    }
  };

  registerBlockType('leadin/hubspot-form-block', {
    title: __('HubSpot Form', 'leadin'),
    description: __('Select and embed a HubSpot form', 'leadin'),
    icon: SprocketIcon,
    category: 'leadin-blocks',
    attributes: {
      portalId: {
        type: 'string',
        default: '',
      },
      formId: {
        type: 'string',
      },
      formName: {
        type: 'string',
      },
      preview: {
        type: 'boolean',
        default: false,
      },
    },
    example: {
      attributes: {
        preview: true,
      },
    },
    edit: editComponent,
    save: (props: any) => <FormBlockSave {...props} />,
  });
}
