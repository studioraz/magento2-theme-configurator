# Theme Configurator for Magento 2.x.x

## HOW TO

#### 1. The set of field is here:

* Admin area -> Content -> Design -> Configuration -> choose Theme's Scope (edit form)
* Edit Form -> Other Settings -> SR Theme Configurator

Paths (for example):
* /admin/theme/design_config/edit/scope/default/
* /admin/theme/design_config/edit/scope/websites/scope_id/1/
* /admin/theme/design_config/edit/scope/stores/scope_id/1/


#### 2. Add new or delete existing field should be done in the file:
src/app/code/SR/ThemeConfigurator/view/adminhtml/ui_component/design_config_form.xml

the Fields can be grouped:

    <fieldset name="sr_theme_configurator"> (<level>1</level>) Root element for SR_ThemeConfigurator
    
    <fieldset name="less_variables"> (<level>2</level>) sub-section
    
    <fieldset name="general"> (<level>3</level>) sub-sub-section
    
    <field name="primary__color" formElement="input"... the element directly

**Important:** Name of **Less**-variable should be used during naming the fields (w/o leading "@" symbol):

example:
* **Less**-variable: _@primary__color_
* name of the field: _primary__color_

**Important:** Use "_" symbol instead of "-" during naming sub-section and sub-sub-sections;

**Note:** Keep block XML-structure when new Field is added;

**Note:** How ***<dataScope*** is composed:

     <dataScope>{sub_section_name}.{sub_sub_section_name}.{field_name}</dataScope> ===> <dataScope>less_variables.general.secondary__color</dataScope>

**Note:** if any sub-section name, sub-sub-section name or field name is changed, that according values in DB should be updated manually (for all scopes);
_The trick is needed to avoid Exception throwing about Invalid Component_;


#### 3. The data is stored in ***core_config_data*** _path="design/sr_theme_configurator/less_variables"_
**Note:** the data is serialized (json-encoded);


#### 4. Ability to reset value of any field exists:
**Example:**
* ***if*** _scope/websites/scope_id/1/_ has saved value of _primary__color_ parameter
* ***then*** the value is **Default** (_Parent scope_);


#### 5. If all values of the Child scope are reset to the value of their Parent scope,
then the record of that Child scope will be deleted as redundant from _core_config_data_ table;

- - - -

#### 10. Behind the scene:

10.1. **_theme_srthemeconfig.less** file within content of variables is generated when Form is saved;

10.2. **_theme.less** file content is updated after the content has been generated; (content is: *@import '_theme_srthemeconfig.less';*);

10.3. the content of the file ***_theme_srthemeconfig.less*** is generated always if the Form is saved (the file is overwritten);

10.4. Custom extra-content of **_theme.less** file is injected only if it is needed (ex: when the content has not been added yet);

* ***ThemeLessVariablesProcessor*** - is the main processor-handler;
* ***LessFileGenerator*** - is the generator of custom file ***_theme_srthemeconfig.less***;
* ***LessFileContentInjector*** - injects custom content into destination file (***_theme.less*** is used by Default);

- - - -

#### 100. Requirements (are needed to correct work):

100.1. Create Custom Theme and register it in the Project;

100.2. Create **_theme.less** file in the Custom Theme
**Example:** app/design/frontend/CustomTheme/base/web/css/source/_theme.less

**Important:** if the file does not exist the Module generates new empty **_theme.less** file

**Suggestion:** copy **_theme.less** file from Parent Theme into Custom Theme (or another child), which is used as Final (for particular  Store View);

- - - -
