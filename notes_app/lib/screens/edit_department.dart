import 'package:flutter/material.dart';
import 'package:notes_app/models/department.dart';
import 'package:notes_app/services/api_service.dart';

class EditDepartmentScreen extends StatefulWidget {
  final Department? department;

  const EditDepartmentScreen({super.key, this.department});

  @override
  _EditDepartmentScreenState createState() => _EditDepartmentScreenState();
}

class _EditDepartmentScreenState extends State<EditDepartmentScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final ApiService _apiService = ApiService();
  bool _isSaving = false;
  String _error = '';

  @override
  void initState() {
    super.initState();
    if (widget.department != null) {
      _nameController.text = widget.department!.name;
    }
  }

  Future<void> _saveDepartment() async {
    if (_formKey.currentState!.validate()) {
      setState(() {
        _isSaving = true;
        _error = '';
      });

      try {
        final department = Department(
          id: widget.department?.id ?? 0,
          name: _nameController.text,
        );

        if (widget.department == null) {
          await _apiService.createDepartment(department, 'your_token_here');
        } else {
          await _apiService.updateDepartment(department, 'your_token_here');
        }

        Navigator.pop(context, true);
      } catch (e) {
        setState(() => _error = e.toString());
      } finally {
        setState(() => _isSaving = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.department == null ? 'Nouveau Département' : 'Modifier Département'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(
                  labelText: 'Nom du département',
                  border: OutlineInputBorder(),
                ),
                validator: (value) => value?.isEmpty ?? true ? 'Ce champ est requis' : null,
              ),
              const SizedBox(height: 20),
              if (_error.isNotEmpty)
                Text(
                  _error,
                  style: const TextStyle(color: Colors.red),
                ),
              ElevatedButton(
                onPressed: _isSaving ? null : _saveDepartment,
                child: _isSaving
                    ? const CircularProgressIndicator()
                    : const Text('Enregistrer'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}