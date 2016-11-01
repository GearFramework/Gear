<?php

namespace gear\interfaces;

interface IIo {}

interface IFileSystem extends IIo {}

interface IFile extends IFileSystem {}

interface IDirectory extends IFileSystem {}