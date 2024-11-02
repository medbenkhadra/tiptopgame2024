"use client";
import React, { useEffect, useState } from "react";
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import DashboardSpinnigLoader from "@/app/components/widgets/DashboardSpinnigLoader";
import {Select} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

interface OptionType {
    id: string;
    title: string;
    description: string;
    name: string;
    label: string;
}
export default function CKEditorComponent({ onChange , content , index, variables }: any) {
    const [loading, setLoading] = useState(true);

    const [autoCompleteValue, setAutoCompleteValue] = useState(null);
    const [suggestions, setSuggestions] = useState<OptionType[]>([]);
    const filterOption = (input: string, item: OptionType) => (item?.label ?? '').toLowerCase().includes(input.toLowerCase());

    const [lastCursorPosition, setLastCursorPosition] = useState(null);
    const [editor, setEditor] = useState('');

    const handleEditorReady = (editorElement:any) => {
        setEditor(editorElement);
        editorElement.model.document.on('change:data', (event: any, data: any) => {
            applyVariableClass(editorElement);
        });
    };

    useEffect(() => {
        const options: OptionType[] = [];
        variables.forEach((variable:any , index:any) => {
            const option: { id: any; label: string; title: any } = {
                id: variable.id,
                label: (variable.id)+"- " + variable.name  ,
                title: variable.id,

            };
            options.push(option as OptionType);
        });
        setSuggestions(options);
    }, [variables]);



    useEffect(() => {
        const handleDOMContentLoaded = () => {
            setLoading(false);

            import('@ckeditor/ckeditor5-react').then((ckeditor) => {
                import('@ckeditor/ckeditor5-build-classic').then((classicEditor) => {
                    const CKEditor = ckeditor.CKEditor;
                    const ClassicEditor = classicEditor.default;

                });
            });

            document.removeEventListener('DOMContentLoaded', handleDOMContentLoaded);
        };

        if (document.readyState === 'complete') {
            handleDOMContentLoaded();
        } else {
            document.addEventListener('DOMContentLoaded', handleDOMContentLoaded);
        }


        return () => {
            document.removeEventListener('DOMContentLoaded', handleDOMContentLoaded);
        };

    }, []);


    const handleAutoCompleteChange = (value:any) => {
        setAutoCompleteValue(value);

        const filteredSuggestions = variables.filter((variable:any) =>
            variable.name.toLowerCase().includes(value.toLowerCase())
        );

        setSuggestions(filteredSuggestions);
    };



    const handleAutoCompleteSelect = (variable: any) => {
        if (lastCursorPosition && content) {
            const axeX = lastCursorPosition[0];
            const axeY = lastCursorPosition[1];
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');

            const paragraph = doc.body.querySelectorAll('p')[axeX];

            // @ts-ignore
            const textBeforeCursor = paragraph.textContent.substring(0, axeY);
            // @ts-ignore
            const textAfterCursor = paragraph.textContent.substring(axeY);

            const newTextNode = doc.createTextNode(`  {{  ${variable} }}  `);

            paragraph.textContent = '';
            paragraph.appendChild(doc.createTextNode(textBeforeCursor));
            paragraph.appendChild(newTextNode);
            paragraph.appendChild(doc.createTextNode(textAfterCursor));


            const serializer = new XMLSerializer();
            const newContent = serializer.serializeToString(doc);

            onChange(index, newContent);
            setAutoCompleteValue(null);

        }else {
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');
            const newTextNode = doc.createTextNode(`  {{  ${variable} }}  `);
            doc.body.appendChild(newTextNode);
            const serializer = new XMLSerializer();
            const newContent = serializer.serializeToString(doc);
            onChange(index, newContent);
            setAutoCompleteValue(null);
        }
    }


    const handleEditorClick = (event: any, editorElement: any) => {
        console.log('handleEditorClick');
        setEditor(editorElement);
        const data = editorElement.getData();
        const cursorPosition = editorElement.model.document.selection.getFirstPosition();
        onChange(index, data);

        console.log('cursorPosition', cursorPosition);

        if (cursorPosition && cursorPosition) {
            setLastCursorPosition(cursorPosition.path);
        }
    };


    const handleEditorChange = (event: any, editorElement: any) => {
        setEditor(editorElement);
        const data = editorElement.getData();
        const cursorPosition = editorElement.model.document.selection.getFirstPosition();
        onChange(index, data);

        console.log('cursorPosition', cursorPosition);

        if (cursorPosition && cursorPosition) {
            setLastCursorPosition(cursorPosition.path);
        }
    };


    const applyVariableClass = (editorElement: any) => {

    };


    const editorConfig = {
        toolbar: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            '|',
            'numberedList',
            'bulletedList',
            '|',
            'alignment',
            '|',
            'link',
            'unlink',
            '|',
            'blockquote',
            'insertTable',
            '|',
            'undo',
            'redo'
        ],

    };

    const groupedVariables = variables.reduce((groups: any, variable: any) => {
        let groupName = 'Autre';
        if (variable.name.toLowerCase().includes('client')) {
            groupName = 'Client';
        } else if (variable.name.toLowerCase().includes('account')) {
            groupName = 'Account';
        }else if (variable.name.toLowerCase().includes('manager')) {
            groupName = 'Manager';
        }else if (variable.name.toLowerCase().includes('admin')) {
            groupName = 'Admin';
        }else if (variable.name.toLowerCase().includes('employee')) {
            groupName = 'Employée';
        } else if (variable.name.toLowerCase().includes('store')) {
            groupName = 'Magasin';
        }

        if (!groups[groupName]) {
            groups[groupName] = [];
        }

        groups[groupName].push(variable);
        return groups;

    }, {});


    return (
        <>
            {loading ? (
                <DashboardSpinnigLoader></DashboardSpinnigLoader>
            ) : (
                <div className={`mt-4 w-100 ${index == "content" ? 'content_template_editor' : 'subject_template_editor' } `}>
                    <CKEditor
                        editor={ClassicEditor}
                        data={content}
                        onReady={(editorElement: any) => {
                            handleEditorReady(editorElement);
                            console.log("CKEditor5 React Component is ready to use!", editor);
                            editorElement.model.document.on('change:data', (event: any, data: any) => {
                                handleEditorReady(editorElement);
                            });
                            editorElement.model.document.on('click', (event: any) => {
                                handleEditorClick(event , editorElement);
                            });

                        }}

                        onChange={handleEditorChange}
                        config={editorConfig}

                    />
                    <Select
                        className={`${styles.autoCompletCkEditorDiv} autoCompletCkEditor`}
                        value={autoCompleteValue}
                        onChange={handleAutoCompleteChange}
                        onSelect={handleAutoCompleteSelect}
                        placeholder="Rajouter une variable"
                        filterOption={filterOption as any}
                        allowClear
                        showSearch
                        key={index}
                        notFoundContent={
                            <span className={`m-4 py-5 px-1`}>Aucun resultat</span>
                        }
                    >
                        {Object.keys(groupedVariables).map((groupName: string) => (
                            <Select.OptGroup key={groupName} label={groupName}>
                                {groupedVariables[groupName].map((variable: any) => (
                                    <Select.Option key={variable.name} value={variable.name}>
                                        {variable.name}
                                    </Select.Option>
                                ))}
                            </Select.OptGroup>
                        ))}


                        {!variables.length && (
                            <>
                                <Select.Option key="no_variables" value="no_variables" disabled>
                                    Aucune variable
                                </Select.Option>
                            </>
                        )}

                    </Select>
                </div>
            )}
        </>
    );
}
