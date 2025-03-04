import React from 'react';


function Table( { className = "", align = 'center', width = '100%', cellpadding = '0', cellspacing = '0', role = 'presentation', children } )
{
    return (
        <table className={ className } align={ align } width={ width } cellPadding={ cellpadding } cellSpacing={ cellspacing } role={ role }>

            <tr>

                <td>{ children }</td>

            </tr>

        </table>
    );
};


export default Table;
