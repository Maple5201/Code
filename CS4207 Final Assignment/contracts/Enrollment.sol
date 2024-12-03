// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract Enrollment {
    struct Module {
        string moduleName;
        uint capacity;
        uint enrolled;
    }

    mapping(string => Module) public modules;
    mapping(string => mapping(address => bool)) public moduleStudents;
    string[] private moduleKeys; 

    function addModule(string memory moduleName, uint capacity) public {
        require(bytes(modules[moduleName].moduleName).length == 0, "Module already exists");

        modules[moduleName] = Module(moduleName, capacity, 0);
        moduleKeys.push(moduleName); 
    }

    function enrollStudent(string memory moduleName) public {
        require(bytes(modules[moduleName].moduleName).length > 0, "Module not found");
        require(modules[moduleName].capacity > modules[moduleName].enrolled, "Module is full");
        require(!moduleStudents[moduleName][msg.sender], "Student already enrolled");

        moduleStudents[moduleName][msg.sender] = true;
        modules[moduleName].enrolled++;
    }

    function getModuleDetails(string memory moduleName) public view returns (string memory, uint, uint) {
        require(bytes(modules[moduleName].moduleName).length > 0, "Module not found");

        Module memory module = modules[moduleName];
        return (module.moduleName, module.capacity, module.enrolled);
    }

    
    function getAllModules() public view returns (string[] memory) {
        return moduleKeys;
    }
}
