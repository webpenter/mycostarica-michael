import React from 'react';
import { RawHTML } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';

interface IMeetingSaveBlockProps {
  attributes: {
    url: string;
  };
}

export default function MeetingSaveBlock({
  attributes,
}: IMeetingSaveBlockProps) {
  const { url } = attributes;

  if (url) {
    return (
      <RawHTML
        {...useBlockProps.save()}
      >{`[hubspot url="${url}" type="meeting"]`}</RawHTML>
    );
  }
  return null;
}
