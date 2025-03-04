import React from "react";


interface TableProps
{
    className ? : string;
    align ? : "left" | "center" | "right";
    width ? : string;
    cellpadding ? : string;
    cellspacing ? : string;
    role ? : string;
    children ? : React.ReactNode;
}


function Table( props : TableProps ) : React.JSX.Element
{
    return (
        <table
            className={ props.className ?? "" }
            align={ props.align ?? "center" }
            width={ props.width ?? '100%' }
            cellPadding={ props.cellpadding ?? '0' }
            cellSpacing={ props.cellspacing ?? '0' }
            role={ props.role ?? 'presentation' }>

            <tr>

                <td>{ props.children }</td>

            </tr>

        </table>
    );
};


export default Table;
