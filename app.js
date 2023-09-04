// Load records only once on initial load and cache them
let records = [];

const ajaxMainRequest  = async (url, method, data) => {
    try {
        let dataBody = data;
        if(data) {
            dataBody = new URLSearchParams(data) ;
        }
        const response = await fetch(
            url,
            {
                method: method,
                header: {
                    'Content-Type' : 'application/x-www.urlencoded'
                },
                body: dataBody 
            }       
        );

        if(response.ok) {
            const result = await response.json();
            return result;
        } else {
            console.log("error network" + response.statusText)
        }
    } catch (error) {
        console.log(error.message);
    }
};

const displayEmployeeGrid = (result) => {
    const main_element  = document.getElementById("main_contents");
    const employee_data = result.map((value, key)=> {

        const theaddata =  `<tr><td> ${value.id} </td>
                            <td> ${value.employee_name} </td>
                             <td> ${value.salary} </td>
                             <td><button type="submit" class="btn btn-success" onclick="editEmployee(${value.id})">Edit</button>
                             <button type="submit" class="btn btn-danger" onclick="deleteEmployee(${value.id})">Delete</button></td>
                             </tr>`;
        return theaddata;
    
    }).join('');
 
    main_element.innerHTML = employee_data; 
};

document.getElementById('insertForm').addEventListener('submit', async (event) => {
   event.preventDefault();
    const formDatavalue = new FormData(event.target);
    const result = await ajaxMainRequest('proces_grid.php?action=create', 'POST', formDatavalue);
    if(result) {
        document.getElementById('insertForm').reset();
        const newRecord = { id: result.id, employee_name: result.employee_name, salary: result.salary };
        records.push(newRecord);
        records = records.sort((a,b) => {
            return b.id - a.id;
        });
        displayEmployeeGrid(records);
    } else {
        console.log("Error inserting");
    }
});


const deleteEmployee = async (id) => {
    const result = await ajaxMainRequest(`proces_grid.php?action=delete&id=${id}`, 'GET');
    if(result) {
        records = records.filter((value)=> {
            return value.id!==id;
        });
        console.log(records);
        displayEmployeeGrid(records);
    }
};

document.getElementById('searchForm').addEventListener('submit',  (event) => {
    event.preventDefault();

    const search_value = event.target.search_employee.value.toLowerCase();
    if(records.length>0 && search_value.length>0) { 
        const filtered_records = records.filter((value)=> {
            if(value.employee_name.toLowerCase().includes(search_value)) {
                return value;
            }
            return false;
        });
        console.log(records);
        displayEmployeeGrid(filtered_records);
    }
 });



const fetchAndGetData = async () => {
    const result = await ajaxMainRequest('proces_grid.php?action=all', 'GET');
    if(result.data) {
        records = result.data;
        displayEmployeeGrid(result.data);
    } else {
        
        console.log("Error occured");
    } 
}


document.addEventListener('DOMContentLoaded', () => {
    fetchAndGetData();
});