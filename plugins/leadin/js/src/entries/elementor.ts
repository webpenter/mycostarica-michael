import elementorWidget from '../elementor/elementoWidget';
import registerFormWidget from '../elementor/FormWidget/registerFormWidget';
import { initBackgroundApp } from '../utils/backgroundAppUtils';
import registerMeetingsWidget from '../elementor/MeetingWidget/registerMeetingWidget';

window.addEventListener('elementor/init', () => {
  initBackgroundApp(() => {
    let FormWidget: any;
    let MeetingsWidget: any;

    const leadinSelectFormItemView = elementorWidget(
      //@ts-expect-error global
      window.elementor,
      {
        widgetName: 'hubspot-form',
        controlSelector: '.elementor-hbspt-form-selector',
        containerSelector: '.hubspot-form-edit-mode',
      },
      (controlContainer: any, widgetContainer: any, setValue: Function) => {
        FormWidget = new registerFormWidget(
          controlContainer,
          widgetContainer,
          setValue
        );
        FormWidget.render();
      },
      () => {
        FormWidget.done();
      }
    );

    const leadinSelectMeetingItemView = elementorWidget(
      //@ts-expect-error global
      window.elementor,
      {
        widgetName: 'hubspot-meeting',
        controlSelector: '.elementor-hbspt-meeting-selector',
        containerSelector: '.hubspot-meeting-edit-mode',
      },
      (controlContainer: any, widgetContainer: any, setValue: Function) => {
        MeetingsWidget = new registerMeetingsWidget(
          controlContainer,
          widgetContainer,
          setValue
        );
        MeetingsWidget.render();
      },
      () => {
        MeetingsWidget.done();
      }
    );

    //@ts-expect-error global
    window.elementor.addControlView(
      'leadinformselect',
      leadinSelectFormItemView
    );
    //@ts-expect-error global
    window.elementor.addControlView(
      'leadinmeetingselect',
      leadinSelectMeetingItemView
    );
  });
});
